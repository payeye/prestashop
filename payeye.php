<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Enum\ShippingProvider;
use PayEye\Lib\Env\Config;
use PayEye\Lib\Tool\JsonHelper;
use PayEye\Lib\Tool\Uuid;
use PrestaShop\Module\PayEye\Admin\Configuration\AdminFormConfiguration;
use PrestaShop\Module\PayEye\Admin\Configuration\ConfigurationField;
use PrestaShop\Module\PayEye\Admin\Configuration\HandleConfiguration;
use PrestaShop\Module\PayEye\Admin\Order\OrderStateService;
use PrestaShop\Module\PayEye\Admin\Order\OrderStatuses;
use PrestaShop\Module\PayEye\Database\Database;
use PrestaShop\Module\PayEye\Entity\PayEyeCartMappingEntity;
use PrestaShop\Module\PayEye\Shared\ShippingMatch\ShippingMatchCollection;
use PrestaShop\Module\PayEye\Translations\OrderStatesTranslations;

class PayEye extends PaymentModule
{
    public const NAMESPACE = 'api-payeye/v1';

    /** @var AuthConfig */
    public $authConfig;

    /** @var ShippingMatchCollection */
    public $shippingMatchCollection;

    /** @var Config */
    public $config;

    /** @var OrderStatuses */
    public $orderStatuses;

    public function __construct()
    {
        $this->name = 'payeye';
        $this->tab = 'payments_gateways';
        $this->version = '0.0.7';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->author = 'PayEye';
        $this->controllers = ['Cart', 'Order', 'OrderUpdate', 'Widget'];
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('PayEye');
        $this->description = $this->l('Description of PayEye');

        $this->authConfig = new AuthConfig(
            Configuration::get(ConfigurationField::SHOP_ID),
            Configuration::get(ConfigurationField::PUBLIC_KEY),
            Configuration::get(ConfigurationField::PRIVATE_KEY)
        );
        $this->shippingMatchCollection = new ShippingMatchCollection($this->getMatchedShippingProviders());
        $this->config = Config::createFromArray(JsonHelper::getArrayFromJsonFile(__DIR__ . '/config.json'));
        $this->orderStatuses = new OrderStatuses(
            (int) Configuration::get(ConfigurationField::PAYMENT_STATUS_WAITING),
            (int) Configuration::get('PS_OS_WS_PAYMENT'),
            (int) Configuration::get('PS_OS_ERROR')
        );
    }

    public function install(): bool
    {
        return
            parent::install() &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            (new Database())->createTable() &&
            $this->installDefaultConfiguration();
    }

    public function hookModuleRoutes(): array
    {
        return [
            'module-payeye-cart' => $this->registerRouter('Cart', 'carts'),
            'module-payeye-cart-promo-codes' => $this->registerRouter('PromoCode', 'carts/promo-codes'),
            'module-payeye-orders' => $this->registerRouter('Order', 'orders'),
            'module-payeye-orders-update' => $this->registerRouter('OrderUpdate', 'orders/status'),
            'module-payeye-widget' => $this->registerRouter('Widget', 'widget'),
            'module-payeye-widget-status' => $this->registerRouter('WidgetStatus', 'widget/status'),
        ];
    }

    public function hookActionFrontControllerSetMedia(): void
    {
        $this->context->controller->registerJavascript(
            'payeye',
            'modules/' . $this->name . '/views/js/script.js',
            [
                'position' => 'footer',
                'inline' => false,
                'priority' => 1000,
            ]
        );

        Media::addJsDef([
            'payeye' => [
                'platform' => 'PRESTASHOP',
                'apiUrl' => $this->context->shop->getBaseURL(true) . self::NAMESPACE,
            ],
        ]);
    }

    public function installDefaultConfiguration(): bool
    {
        $orderStateService = new OrderStateService($this);

        return
            Configuration::updateValue(ConfigurationField::ADMIN_TEST_MODE, '1') &&
            Configuration::updateValue(
                ConfigurationField::PAYMENT_STATUS_WAITING,
                $orderStateService->addOrderState(ConfigurationField::PAYMENT_STATUS_WAITING, OrderStatesTranslations::PAYMENT_STATUS_WAITING)
            );
    }

    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $shopID = (string) Tools::getValue(ConfigurationField::SHOP_ID);
            $publicKey = (string) Tools::getValue(ConfigurationField::PUBLIC_KEY);
            $privateKey = (string) Tools::getValue(ConfigurationField::PRIVATE_KEY);
            $mode = (string) Tools::getValue(ConfigurationField::ADMIN_TEST_MODE);

            if (
                Configuration::updateValue(ConfigurationField::SHOP_ID, $shopID) &&
                Configuration::updateValue(ConfigurationField::PUBLIC_KEY, $publicKey) &&
                Configuration::updateValue(ConfigurationField::PRIVATE_KEY, $privateKey) &&
                Configuration::updateValue(ConfigurationField::ADMIN_TEST_MODE, $mode) &&
                Configuration::updateValue(ConfigurationField::SHIPPING_MATCHING, HandleConfiguration::handleMatching(Tools::getAllValues()))
            ) {
                $output = $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output = $this->displayError($this->l('Invalid Configuration value'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm(): string
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true);
        $formConfiguration = new AdminFormConfiguration($this);

        $form['auth'] = $formConfiguration->authFormType();

        $carriers = array_map(function ($carrier) {
            return [
                'type' => 'select',
                'label' => $carrier['name'],
                'name' => ConfigurationField::SHIPPING_MATCHING . $carrier['id_carrier'],
                'desc' => $this->l('Select shipping providers for your carrier'),
                'carrierId' => $carrier['id_carrier'],
                'options' => [
                    'query' => $this->getAvailableShippingQuery(),
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
        }, $carriers);

        $form['shippingMatching'] = $formConfiguration->shippingMatchingFormType($carriers);

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
                ConfigurationField::SHOP_ID => Configuration::get(ConfigurationField::SHOP_ID),
                ConfigurationField::PUBLIC_KEY => Configuration::get(ConfigurationField::PUBLIC_KEY),
                ConfigurationField::PRIVATE_KEY => Configuration::get(ConfigurationField::PRIVATE_KEY),
                ConfigurationField::ADMIN_TEST_MODE => Configuration::get(ConfigurationField::ADMIN_TEST_MODE),
            ],
            $this->getConfigFieldsValuesForCarrierMatching($matchShipping)
        );
    }

    public function uninstall(): bool
    {
        foreach (ConfigurationField::getUninstallFields() as $value) {
            Configuration::deleteByName($value);
        }

        return parent::uninstall();
    }

    public function isAdminTestMode(): bool
    {
        return Configuration::get(ConfigurationField::ADMIN_TEST_MODE);
    }

    private function getMatchedShippingProviders(): array
    {
        $matching = Configuration::get(ConfigurationField::SHIPPING_MATCHING);

        return $matching ? json_decode($matching, true) : [];
    }

    public function hookActionCartSave(array $payload): void
    {
        /** @var Cart $cart */
        $cart = $payload['cart'];
        $cartId = $cart->id;

        if ($cartId === null) {
            return;
        }

        $cartMapping = PayEyeCartMapping::findByCartId($cartId);

        if ($cartMapping) {
            return;
        }

        $cartMapping = new PayEyeCartMapping();
        $cartMapping->setEntity(
            PayEyeCartMappingEntity::builder()
                ->setCartId($cart->id)
                ->setOpen(false)
                ->setUuid(Uuid::generate())
        );

        try {
            $cartMapping->add();
        } catch (PrestaShopDatabaseException|PrestaShopException $e) {
            // do nothing
        }
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

    private function getAvailableShippingQuery(): array
    {
        return [
            [
                'id' => false,
                'name' => 'Select matching',
            ],
            [
                'id' => ShippingProvider::COURIER,
                'name' => $this->l('Courier'),
            ],
            [
                'id' => ShippingProvider::SELF_PICKUP,
                'name' => $this->l('Self pickup'),
            ],
            [
                'id' => ShippingProvider::INPOST,
                'name' => $this->l('Inpost'),
            ],
            [
                'id' => ShippingProvider::DHL,
                'name' => $this->l('Pickup Point DHL'),
            ],
        ];
    }

    private function registerRouter(string $controller, string $path): array
    {
        return [
            'controller' => $controller,
            'rule' => self::NAMESPACE . '/' . $path,
            'keywords' => [],
            'params' => [
                'fc' => 'module',
                'module' => $this->name,
            ],
        ];
    }
}
