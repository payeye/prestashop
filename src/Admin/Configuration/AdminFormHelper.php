<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

class AdminFormHelper
{
    /** @var \Module */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function installFormType(): array
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
                        'label' => $this->module->l('Admin Test'),
                        'name' => ConfigurationField::ADMIN_TEST_MODE,
                        'desc' => 'Show QR Code only for admin role. Enable mode before go live and check the PayEye flow.',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('No'),
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
}
