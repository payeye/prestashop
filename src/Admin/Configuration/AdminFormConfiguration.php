<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

use PayEye\Lib\Enum\ShippingProvider;
use PayEye\Lib\Enum\WidgetButtonStyles;
use PayEye\Lib\Enum\WidgetModes;

class AdminFormConfiguration
{
    /** @var \Module */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function authFormType($shop_country_name): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Settings configurations for PayEye Payments'),
                    'icon' => 'icon-th',
                ],
                'input' => [
                    ($shop_country_name == '') ? [
                        'type' => 'desc',
                        'name' => 'error_field',
                        'label' => '',
                        'desc' => '<span style="color:red;font-weight:bold;font-size: 14px;">' . $this->module->l('The store address has not been saved!') . '<br></span>' . $this->module->l('Please go to the "Store parameters" -> "Contact" -> "Stores" tab, then in the "Contact details" section, complete the "Country" field and save the form.'),
                        ] : [],
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
                        'type' => 'select',
                        'label' => $this->module->l('Widget Mode'),
                        'desc' => $this->module->l('Choose to use floating widget or inject button in elements with id or class "payeye-run-widget"'),
                        'name' => ConfigurationField::WIDGET_MODE,
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => WidgetModes::FLOATING,   // The value of the 'value' attribute of the <option> tag.
                                    'name' => $this->module->l('Floating widget'),  // The text inside the <option> tag.
                                ],
                                [
                                    'id_option' => WidgetModes::ON_CLICK,
                                    'name' => $this->module->l('On click button'),
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->module->l('Widget on click button style'),
                        'desc' => $this->module->l('Choose to load pre-styled button or simple link'),
                        'name' => ConfigurationField::ON_CLICK_BUTTON_STYLE,
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => WidgetButtonStyles::STYLED_GREEN,   // The value of the 'value' attribute of the <option> tag.
                                    'name' => $this->module->l('Green Button'),  // The text inside the <option> tag.
                                ],
                                [
                                    'id_option' => WidgetButtonStyles::STYLED_WHITE,   // The value of the 'value' attribute of the <option> tag.
                                    'name' => $this->module->l('White Button'),  // The text inside the <option> tag.
                                ],
                                [
                                    'id_option' => WidgetButtonStyles::CUSTOM,
                                    'name' => $this->module->l('Simple link'),
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
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
                        'type' => 'select',
                        'label' => $this->module->l('Widget side'),
                        'desc' => $this->module->l('Where is widget placed on left or right side'),
                        'name' => ConfigurationField::WIDGET_UI_SIDE,
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => 'LEFT',   // The value of the 'value' attribute of the <option> tag.
                                    'name' => $this->module->l('Left'),  // The text inside the <option> tag.
                                ],
                                [
                                    'id_option' => 'RIGHT',
                                    'name' => $this->module->l('Right'),
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Widget bottom position'),
                        'desc' => $this->module->l('Distance from the bottom screen (unit px). Default 20.'),
                        'name' => ConfigurationField::WIDGET_UI_BOTTOM,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Widget side position'),
                        'desc' => $this->module->l('Distance from the side screen (unit px). Default 20.'),
                        'name' => ConfigurationField::WIDGET_UI_SIDE_POSITION,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->module->l('Widget z index'),
                        'name' => ConfigurationField::WIDGET_UI_ZINDEX,
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
