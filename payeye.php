<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Env\Config;
use PayEye\Lib\Tool\JsonHelper;
use PrestaShop\Module\PayEye\Admin\Configuration\AdminFormConfiguration;
use PrestaShop\Module\PayEye\Admin\Configuration\ConfigurationField;
use PrestaShop\Module\PayEye\Admin\Configuration\HandleConfiguration;
use PrestaShop\Module\PayEye\Admin\Order\OrderStateService;
use PrestaShop\Module\PayEye\Admin\Order\OrderStatuses;
use PrestaShop\Module\PayEye\Admin\Widget\WidgetUI;
use PrestaShop\Module\PayEye\Database\Database;
use PrestaShop\Module\PayEye\Module\HookActionCartSave;
use PrestaShop\Module\PayEye\Module\HookModuleRoutes;
use PrestaShop\Module\PayEye\Shared\ShippingMatch\ShippingMatchCollection;
use PrestaShop\Module\PayEye\Translations\OrderStatesTranslations;

class PayEye extends PaymentModule
{
    public const NAMESPACE = 'module-payeye/v1';

    /** @var AuthConfig */
    public $authConfig;

    /** @var ShippingMatchCollection */
    public $shippingMatchCollection;

    /** @var Config */
    public $config;

    /** @var OrderStatuses */
    public $orderStatuses;

    /** @var WidgetUI */
    public $widgetUI;

    /** @var bool */
    public $testMode;

    public function __construct()
    {
        $this->name = 'payeye';
        $this->tab = 'payments_gateways';
        $this->version = '0.0.30';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->author = 'PayEye';
        $this->controllers = ['Cart', 'Order', 'OrderUpdate', 'Widget', 'Return'];
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('e-payeye payments');
        $this->description = $this->l('With just one click, you can pay securely online for your purchases.');

        $this->authConfig = new AuthConfig(
            Configuration::get(ConfigurationField::SHOP_ID),
            Configuration::get(ConfigurationField::PUBLIC_KEY),
            Configuration::get(ConfigurationField::PRIVATE_KEY)
        );
        $this->shippingMatchCollection = new ShippingMatchCollection($this->getMatchedShippingProviders());
        $this->config = Config::createFromArray(JsonHelper::getArrayFromJsonFile(__DIR__ . '/config.json'));
        $this->orderStatuses = new OrderStatuses(
            (int) Configuration::get(ConfigurationField::PAYMENT_STATUS_WAITING),
            (int) Configuration::get('PS_OS_PAYMENT'),
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get(ConfigurationField::RETURN_REQUEST)
        );
        $this->widgetUI = new WidgetUI(
            (int) Configuration::get(ConfigurationField::WIDGET_UI_BOTTOM),
            (bool) Configuration::get(ConfigurationField::WIDGET_UI_MOBILE_OPEN)
        );
        $this->testMode = (bool) Configuration::get(ConfigurationField::TEST_MODE);
    }

    public function install(): bool
    {
        return
            parent::install()
            && (new Database())->createTable()
            && $this->installDefaultConfiguration()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('actionCartSave')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('adminOrder')
            && $this->registerHook('actionPayEyeApiBeforeCreateOrder');
    }

    public function hookModuleRoutes(): array
    {
        return (new HookModuleRoutes($this))();
    }

    public function hookActionFrontControllerSetMedia(): void
    {
        // @TODO e-payeye actions move to lib
        $actions = Tools::getValue('epayeyePreActions', '');
        $actions = explode(',', $actions);

        if (in_array('clearCart', $actions, true)) {
            //            Context::getContext()->cart->delete();
            //            $url = $_SERVER['REQUEST_URI'];
            //            $url = str_replace(['clearCart', 'clearCart,'], '', $url);
            //
            //            Tools::redirect($url);
        }

        $this->context->controller->registerJavascript(
            'payeye',
            $this->getPathUri() . 'views/js/script.js',
            [
                'position' => 'footer',
                'inline' => false,
                'priority' => 1000,
                'version' => $this->version,
            ]
        );

        if ($this->testMode) {
            $this->context->controller->registerStylesheet(
                'payeye-test-css-mode',
                $this->getPathUri() . 'views/css/test-mode.css'
            );
        }

        Media::addJsDef([
            'payeye' => [
                'platform' => 'PRESTASHOP',
                'apiUrl' => $this->context->shop->getBaseURL(true) . self::NAMESPACE,
                'ui' => [
                    'position' => [
                        'bottom' => $this->widgetUI->getBottom() . 'px',
                    ],
                    'mobile' => [
                        'open' => $this->widgetUI->getMobileOpen(),
                    ],
                ],
            ],
        ]);
    }

    public function hookActionAdminControllerSetMedia(): void
    {
        if ('AdminOrders' === Tools::getValue('controller')) {
            $this->context->controller->addJS(
                $this->getPathUri() . 'views/js/return.js?version=' . $this->version,
                false
            );
        }
    }

    public function installDefaultConfiguration(): bool
    {
        $orderStateService = new OrderStateService($this);

        return
            Configuration::updateValue(
                ConfigurationField::PAYMENT_STATUS_WAITING,
                $orderStateService->addOrderState(ConfigurationField::PAYMENT_STATUS_WAITING, OrderStatesTranslations::PAYMENT_STATUS_WAITING)
            )
            && Configuration::updateValue(
                ConfigurationField::RETURN_REQUEST,
                $orderStateService->addOrderState(ConfigurationField::RETURN_REQUEST, OrderStatesTranslations::RETURN_REQUEST, true)
            )
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_BOTTOM, 20)
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN, 0)
            && Configuration::updateValue(ConfigurationField::TEST_MODE, 1);
    }

    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $testMode = (int) Tools::getValue(ConfigurationField::TEST_MODE);
            $shopID = (string) Tools::getValue(ConfigurationField::SHOP_ID);
            $publicKey = (string) Tools::getValue(ConfigurationField::PUBLIC_KEY);
            $privateKey = (string) Tools::getValue(ConfigurationField::PRIVATE_KEY);
            $widgetUiBottom = (int) Tools::getValue(ConfigurationField::WIDGET_UI_BOTTOM);
            $widgetUiMobileOpen = (bool) Tools::getValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN);

            if ($widgetUiBottom === 0 || $widgetUiBottom < 0) {
                $widgetUiBottom = 20;
            }

            if (
                Configuration::updateValue(ConfigurationField::TEST_MODE, $testMode)
                && Configuration::updateValue(ConfigurationField::SHOP_ID, $shopID)
                && Configuration::updateValue(ConfigurationField::PUBLIC_KEY, $publicKey)
                && Configuration::updateValue(ConfigurationField::PRIVATE_KEY, $privateKey)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_BOTTOM, $widgetUiBottom)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN, $widgetUiMobileOpen)
                && Configuration::updateValue(ConfigurationField::SHIPPING_MATCHING, HandleConfiguration::handleMatching(Tools::getAllValues()))
            ) {
                $output = $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output = $this->displayError($this->l('Invalid Configuration value'));
            }
        }

        $output .= $this->fetch('module:' . $this->name . '/views/templates/admin/info.tpl');
        return $output . $this->displayForm();
    }

    public function displayForm(): string
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, CarrierCore::ALL_CARRIERS);
        $formConfiguration = new AdminFormConfiguration($this);

        $form['auth'] = $formConfiguration->authFormType();

        $carriers = array_map(function ($carrier) use ($formConfiguration) {
            return [
                'type' => 'select',
                'label' => $carrier['name'],
                'name' => ConfigurationField::SHIPPING_MATCHING . $carrier['id_carrier'],
                'desc' => $this->l('Select shipping providers for your carrier'),
                'carrierId' => $carrier['id_carrier'],
                'options' => [
                    'query' => $formConfiguration->getAvailableShippingQuery(),
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
        }, $carriers);

        $form['shippingMatching'] = $formConfiguration->shippingMatchingFormType($carriers);
        $form['widget'] = $formConfiguration->widgetFormType();

        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;
        $helper->default_form_language = $this->context->language->id;
        $helper->fields_value = $this->getConfigFieldsValues($carriers);

        return $helper->generateForm($form);
    }

    public function getConfigFieldsValues(array $matchShipping): array
    {
        return array_merge(
            [
                ConfigurationField::TEST_MODE => Configuration::get(ConfigurationField::TEST_MODE),
                ConfigurationField::SHOP_ID => Configuration::get(ConfigurationField::SHOP_ID),
                ConfigurationField::PUBLIC_KEY => Configuration::get(ConfigurationField::PUBLIC_KEY),
                ConfigurationField::PRIVATE_KEY => Configuration::get(ConfigurationField::PRIVATE_KEY),
            ],
            $this->getConfigFieldsValuesForCarrierMatching($matchShipping),
            [
                ConfigurationField::WIDGET_UI_BOTTOM => Configuration::get(ConfigurationField::WIDGET_UI_BOTTOM),
                ConfigurationField::WIDGET_UI_MOBILE_OPEN => Configuration::get(ConfigurationField::WIDGET_UI_MOBILE_OPEN),
            ]
        );
    }

    public function uninstall(): bool
    {
        foreach (ConfigurationField::getUninstallFields() as $value) {
            Configuration::deleteByName($value);
        }

        return parent::uninstall()
            && $this->unregisterHook('actionPayEyeApiBeforeCreateOrder');
    }

    private function getMatchedShippingProviders(): array
    {
        $matching = Configuration::get(ConfigurationField::SHIPPING_MATCHING);

        return $matching ? json_decode($matching, true) : [];
    }

    public function hookActionCartSave(array $payload): void
    {
        (new HookActionCartSave($this))($payload);
    }

    public function hookAdminOrder($params): string
    {
        $orderId = (int) $params['id_order'];
        $order = new Order($orderId);

        if ($order->id === null) {
            return '';
        }

        $customer = new Customer($order->id_customer);

        $this->smarty->assign('PAYEYE_RETURNS', [
            'url' => $this->context->link->getAdminLink('AdminAjaxReturn'),
            'orderId' => (int) $params['id_order'],
            'fullName' => $customer->firstname . ' ' . $customer->lastname,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/admin/return.tpl');
    }

    private function getConfigFieldsValuesForCarrierMatching(array $carrierMatching): array
    {
        $matchedShippingProviders = $this->getMatchedShippingProviders();

        $carrier = [];
        foreach ($carrierMatching as $item) {
            foreach ($matchedShippingProviders as $value) {
                if ($value['carrierId'] === $item['carrierId']) {
                    $carrier[$item['name']] = $value['provider'];
                }
            }

            if (isset($carrier[$item['name']]) === false) {
                $carrier[$item['name']] = false;
            }
        }

        return $carrier;
    }
}
