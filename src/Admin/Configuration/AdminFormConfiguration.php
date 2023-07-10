<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

use PayEye\Lib\Enum\ShippingProvider;

class AdminFormConfiguration
{
    /** @var \Module */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function authFormType(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Settings configurations for PayEye Payments'),
                    'icon' => 'icon-th',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Test Mode'),
                        'desc' => $this->module->l("Enable this mode before your store is approved by PayEye's technical department"),
                        'name' => ConfigurationField::TEST_MODE,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Shop ID'),
                        'name' => ConfigurationField::SHOP_ID,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Public Key'),
                        'name' => ConfigurationField::PUBLIC_KEY,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Private Key'),
                        'name' => ConfigurationField::PRIVATE_KEY,
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
    }

    public function shippingMatchingFormType(array $input): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Carrier Matching for PayEye Payments'),
                    'icon' => 'icon-th',
                ],
                'input' => $input,
                'submit' => [
                    'title' => $this->module->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
    }

    public function widgetFormType(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Widget UI'),
                    'icon' => 'icon-th',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Mobile launcher first'),
                        'desc' => $this->module->l('Enable launcher first'),
                        'name' => ConfigurationField::WIDGET_UI_MOBILE_OPEN,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Widget bottom position'),
                        'desc' => $this->module->l('Distance from the bottom screen (unit px). Default 20.'),
                        'name' => ConfigurationField::WIDGET_UI_BOTTOM,
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
    }

    public function getAvailableShippingQuery(): array
    {
        return [
            [
                'id' => false,
                'name' => 'Select matching',
            ],
            [
                'id' => ShippingProvider::COURIER,
                'name' => $this->module->l('Courier'),
            ],
            [
                'id' => ShippingProvider::SELF_PICKUP,
                'name' => $this->module->l('Self pickup'),
            ],
            [
                'id' => ShippingProvider::INPOST,
                'name' => $this->module->l('Inpost'),
            ],
            [
                'id' => ShippingProvider::DHL,
                'name' => $this->module->l('Pickup Point DHL'),
            ],
        ];
    }
}
