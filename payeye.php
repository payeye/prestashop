<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Auth\AuthService;
use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Enum\WidgetButtonStyles;
use PayEye\Lib\Enum\WidgetModes;
use PayEye\Lib\Env\Config;
use PayEye\Lib\HttpClient\Model\PluginStatusRequest;
use PayEye\Lib\HttpClient\PayEyeHttpClient;
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


    /** @var int */
    private $apiVersion = 2;


    public function __construct()
    {
        $this->name = 'payeye';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
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

        $this->authConfig = AuthConfig::create(
            Configuration::get(ConfigurationField::SHOP_ID),
            Configuration::get(ConfigurationField::PUBLIC_KEY),
            Configuration::get(ConfigurationField::PRIVATE_KEY)
        );
        $this->shippingMatchCollection = new ShippingMatchCollection($this->getMatchedShippingProviders());
        $this->config = Config::createFromArray(JsonHelper::getArrayFromJsonFile(__DIR__ . '/config.json'));
        $this->orderStatuses = new OrderStatuses(
            (int)Configuration::get(ConfigurationField::PAYMENT_STATUS_WAITING),
            (int)Configuration::get('PS_OS_PAYMENT'),
            (int)Configuration::get('PS_OS_ERROR'),
            (int)Configuration::get(ConfigurationField::RETURN_REQUEST)
        );
        $this->widgetUI = new WidgetUI();
        $this->widgetUI
            ->setBottom((int)Configuration::get(ConfigurationField::WIDGET_UI_BOTTOM))
            ->setMobileOpen((bool)Configuration::get(ConfigurationField::WIDGET_UI_MOBILE_OPEN))
            ->setWidgetVisible((bool)Configuration::get(ConfigurationField::WIDGET_UI_WIDGET_VISIBLE))
            ->setSide((string)Configuration::get(ConfigurationField::WIDGET_UI_SIDE))
            ->setSidePosition((int)Configuration::get(ConfigurationField::WIDGET_UI_SIDE_POSITION))
            ->setZIndex((int)Configuration::get(ConfigurationField::WIDGET_UI_ZINDEX))
            ->setWidgetMode((string)Configuration::get(ConfigurationField::WIDGET_MODE))
            ->setOnClickButtonStyle((string)Configuration::get(ConfigurationField::ON_CLICK_BUTTON_STYLE));
        $this->testMode = (bool)Configuration::get(ConfigurationField::TEST_MODE);
    }

    public function disable($force_all = false)
    {
        $this->sendPluginStatus('PLUGIN_DEACTIVATED');
        return parent::disable($force_all);
    }

    public function enable($force_all = false)
    {
        $this->sendPluginStatus('PLUGIN_ACTIVATED');
        return parent::enable($force_all);
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
            && ($this->isPrestaShop178OrLater() ? $this->registerHook('actionCartUpdateQuantityBefore') : $this->registerHook('ActionBeforeCartUpdateQty'))
            && $this->registerHook('actionObjectProductInCartDeleteAfter')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('adminOrder')
            && $this->registerHook('actionPayEyeApiBeforeCreateOrder')
            && $this->registerHook('actionFilterPayeyePromoCodes');
    }

    private function isPrestaShop178OrLater(): bool
    {
        return version_compare(_PS_VERSION_, '1.7.8', '>=');
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
                'side' => $this->widgetUI->getSide(),
                'platform' => 'PRESTASHOP',
                'apiUrl' => $this->context->shop->getBaseURL(true) . 'module-payeye/v' . $this->getApiVersion(),
                'ui' => [
                    'position' => [
                        'bottom' => $this->widgetUI->getBottom() . 'px',
                        'side' => $this->widgetUI->getSidePosition() . 'px',
                    ],
                    'zIndex' => $this->widgetUI->getZIndex(),
                    'mobile' => [
                        'open' => $this->widgetUI->getMobileOpen(),
                    ],
                    'widget' => [
                        'visible' => $this->widgetUI->getWidgetVisible(),
                    ],
                ],
                'widgetMode' => $this->widgetUI->getWidgetMode(),
                'widgetModeBtnStyle' => $this->widgetUI->getOnClickButtonStyle()
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
        if ('AdminModules' === Tools::getValue('controller') && 'payeye' === Tools::getValue('configure')) {
            $this->context->controller->addJS(
                $this->getPathUri() . 'views/js/settings.js?version=' . $this->version,
                false
            );
        }

        $uiFields = ConfigurationField::getUiFields();
        $hideFloating = [
            ConfigurationField::ON_CLICK_BUTTON_STYLE
        ];
        $showFloating = array_diff($uiFields, $hideFloating, [ConfigurationField::WIDGET_MODE]);

        Media::addJsDef([
            'payeyeAdmin' => [
                "toggles" => [
                    [
                        'id' => ConfigurationField::WIDGET_MODE,
                        'values' => [
                            WidgetModes::FLOATING => [
                                'hide' => $hideFloating,
                                'show' => $showFloating
                            ],
                            WidgetModes::ON_CLICK => [
                                'hide' => $showFloating,
                                'show' => $hideFloating
                            ],
                        ]
                    ]

                ]
            ],
        ]);
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
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_SIDE_POSITION, 20)
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_SIDE, 'RIGHT')
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_ZINDEX, 99998)
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN, 0)
            && Configuration::updateValue(ConfigurationField::WIDGET_UI_WIDGET_VISIBLE, 1)
            && Configuration::updateValue(ConfigurationField::WIDGET_MODE, WidgetModes::FLOATING)
            && Configuration::updateValue(ConfigurationField::ON_CLICK_BUTTON_STYLE, WidgetButtonStyles::STYLED_GREEN)
            && Configuration::updateValue(ConfigurationField::TEST_MODE, 1);
    }

    private function sendPluginStatus($pluginEvent): void
    {
        $pluginMode = Configuration::get(ConfigurationField::TEST_MODE) ? 'INTEGRATION' : 'PRODUCTION';

        $request = PluginStatusRequest::create(
            $this->getApiVersion(),
            $this->authConfig->getShopId(),
            $pluginMode,
            'PHP ' . phpversion(),
            _PS_VERSION_,
            $this->version,
            $pluginEvent,
            null
        );

        $auth = AuthService::create(
            HashService::create($this->authConfig),
            SignatureFrom::PLUGIN_UPDATE_STATUS_REQUEST,
            $request->toArray()
        );

        $httpClient = PayEyeHttpClient::create($this->config, $this->getApiVersion());
        $httpClient->sendPluginStatus($request, $auth);
    }

    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $testMode = (int)Tools::getValue(ConfigurationField::TEST_MODE);
            $shopID = (string)Tools::getValue(ConfigurationField::SHOP_ID);
            $publicKey = (string)Tools::getValue(ConfigurationField::PUBLIC_KEY);
            $privateKey = (string)Tools::getValue(ConfigurationField::PRIVATE_KEY);
            $widgetUiBottom = Tools::getValue(ConfigurationField::WIDGET_UI_BOTTOM);
            $widgetUiSidePosition = Tools::getValue(ConfigurationField::WIDGET_UI_SIDE_POSITION);
            $widgetUiSide = (string)Tools::getValue(ConfigurationField::WIDGET_UI_SIDE);
            $widgetUiZIndex = Tools::getValue(ConfigurationField::WIDGET_UI_ZINDEX);
            $widgetUiMobileOpen = (bool)Tools::getValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN);
            $widgetVisible = (bool)Tools::getValue(ConfigurationField::WIDGET_UI_WIDGET_VISIBLE);
            $widgetMode = (string)Tools::getValue(ConfigurationField::WIDGET_MODE);
            $onClickButtonStyle = (string)Tools::getValue(ConfigurationField::ON_CLICK_BUTTON_STYLE);

            if (!is_numeric($widgetUiBottom) || $widgetUiBottom < 0) {
                $widgetUiBottom = 20;
            }
            if (!is_numeric($widgetUiSidePosition) || $widgetUiSidePosition < 0) {
                $widgetUiSidePosition = 20;
            }
            if (!is_numeric($widgetUiZIndex) || $widgetUiZIndex < 0) {
                $widgetUiZIndex = 99998;
            }

            if (
                Configuration::updateValue(ConfigurationField::TEST_MODE, $testMode)
                && Configuration::updateValue(ConfigurationField::SHOP_ID, $shopID)
                && Configuration::updateValue(ConfigurationField::PUBLIC_KEY, $publicKey)
                && Configuration::updateValue(ConfigurationField::PRIVATE_KEY, $privateKey)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_BOTTOM, $widgetUiBottom)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_SIDE_POSITION, $widgetUiSidePosition)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_SIDE, $widgetUiSide)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_ZINDEX, $widgetUiZIndex)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_MOBILE_OPEN, $widgetUiMobileOpen)
                && Configuration::updateValue(ConfigurationField::WIDGET_UI_WIDGET_VISIBLE, $widgetVisible)
                && Configuration::updateValue(ConfigurationField::WIDGET_MODE, $widgetMode)
                && Configuration::updateValue(ConfigurationField::ON_CLICK_BUTTON_STYLE, $onClickButtonStyle)
                && Configuration::updateValue(ConfigurationField::SHIPPING_MATCHING, HandleConfiguration::handleMatching(Tools::getAllValues())) // ten update nie działa chyba ze działa ale front to źle wyświetla
            ) {
                $output = $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output = $this->displayError($this->l('Invalid Configuration value'));
            }

            $this->sendPluginStatus('PLUGIN_ACTIVATED');
        }

        $output .= $this->checkVersion();

        return $output . $this->displayForm();
    }

    public function displayForm(): string
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, CarrierCore::ALL_CARRIERS);
        $formConfiguration = new AdminFormConfiguration($this);

        $shop_country_name = Configuration::get('PS_SHOP_COUNTRY');
        $form['auth'] = $formConfiguration->authFormType($shop_country_name);

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
                ConfigurationField::WIDGET_MODE => Configuration::get(ConfigurationField::WIDGET_MODE),
                ConfigurationField::ON_CLICK_BUTTON_STYLE => Configuration::get(ConfigurationField::ON_CLICK_BUTTON_STYLE),
                ConfigurationField::WIDGET_UI_BOTTOM => Configuration::get(ConfigurationField::WIDGET_UI_BOTTOM),
                ConfigurationField::WIDGET_UI_SIDE_POSITION => Configuration::get(ConfigurationField::WIDGET_UI_SIDE_POSITION),
                ConfigurationField::WIDGET_UI_SIDE => Configuration::get(ConfigurationField::WIDGET_UI_SIDE),
                ConfigurationField::WIDGET_UI_ZINDEX => Configuration::get(ConfigurationField::WIDGET_UI_ZINDEX),
                ConfigurationField::WIDGET_UI_MOBILE_OPEN => Configuration::get(ConfigurationField::WIDGET_UI_MOBILE_OPEN),
                ConfigurationField::WIDGET_UI_WIDGET_VISIBLE => Configuration::get(ConfigurationField::WIDGET_UI_WIDGET_VISIBLE),
            ]
        );
    }

    public function uninstall(): bool
    {
        $this->sendPluginStatus('PLUGIN_DEACTIVATED');

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

    public function hookActionCartUpdateQuantityBefore(array $payload): void
    {
        (new HookActionCartSave($this))($payload);
    }

    public function hookActionBeforeCartUpdateQty(array $payload): void
    {
        (new HookActionCartSave($this))($payload);
    }

    public function hookActionObjectProductInCartDeleteAfter(array $payload): void
    {
        (new HookActionCartSave($this))($payload);
    }

    public function hookAdminOrder($params): string
    {
        $orderId = (int)$params['id_order'];
        $order = new Order($orderId);

        if ($order->id === null) {
            return '';
        }

        $customer = new Customer($order->id_customer);

        $this->smarty->assign('PAYEYE_RETURNS', [
            'url' => $this->context->link->getAdminLink('AdminAjaxReturn'),
            'orderId' => (int)$params['id_order'],
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
                if ($value['carrierId'] == $item['carrierId']) {
                    $carrier[$item['name']] = $value['provider'];
                }
            }

            if (isset($carrier[$item['name']]) === false) {
                $carrier[$item['name']] = false;
            }
        }

        return $carrier;
    }

    private function checkVersion(): string
    {
        $output = '';

        try {
            $response = \PayEye\Lib\HttpClient\Infrastructure\HttpClient::get('https://static.payeye.com/e-commerce/modules/prestashop/e-payeye/version.json')->getArrayResponse();

            $this->smarty->assign('PAYEYE_MODULE_VERSION', [
                'url' => $response['url'],
                'current' => $this->version,
                'version' => $response['version'],
                'update' => version_compare($response['version'], $this->version) === 1,
            ]);

            $output .= $this->fetch('module:' . $this->name . '/views/templates/admin/info.tpl');
        } catch (\PayEye\Lib\HttpClient\Exception\HttpException $e) {
            // do nothing
        }

        return $output;
    }

    public function getApiVersion(): int
    {
        return $this->apiVersion;
    }
}
