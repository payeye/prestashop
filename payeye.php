<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Payeye extends PaymentModule
{
    public const NAMESPACE = 'api-payeye/v1';

    public function __construct()
    {
        $this->name = 'payeye';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => '1.7'];
        $this->author = 'PayEye';
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('PayEye');
        $this->description = $this->l('Description of PayEye');
    }

    public function install()
    {
        return
            parent::install() &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('moduleRoutes');
    }

    public function hookModuleRoutes(): array
    {
        return [
            'module-payeye-carts' => [
                'controller' => 'Cart',
                'rule' => self::NAMESPACE . '/carts',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }
}
