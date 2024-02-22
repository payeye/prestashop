<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class ConfigurationField
{
    public const TEST_MODE = 'PAYEYE_TEST_MODE';
    public const SHOP_ID = 'PAYEYE_SHOP_ID';
    public const PUBLIC_KEY = 'PAYEYE_PUBLIC_KEY';
    public const PRIVATE_KEY = 'PAYEYE_PRIVATE_KEY';
    public const WIDGET_UI_BOTTOM = 'PAYEYE_WIDGET_UI_BOTTOM';
    public const WIDGET_UI_SIDE_POSITION = 'PAYEYE_WIDGET_UI_SIDE_POSITION';
    public const WIDGET_UI_SIDE = 'PAYEYE_WIDGET_UI_SIDE';
    public const WIDGET_UI_ZINDEX = 'PAYEYE_WIDGET_UI_ZINDEX';
    public const WIDGET_UI_MOBILE_OPEN = 'PAYEYE_WIDGET_UI_MOBILE_OPEN';
    public const WIDGET_UI_WIDGET_VISIBLE = 'PAYEYE_WIDGET_UI_WIDGET_VISIBLE';
    public const SHIPPING_MATCHING = 'PAYEYE_SHIPPING_MATCHING';
    public const PAYMENT_STATUS_WAITING = 'PAYEYE_PAYMENT_STATUS_WAITING';
    public const RETURN_REQUEST = 'PAYEYE_RETURN_REQUEST';
    public const WIDGET_MODE = 'PAYEYE_WIDGET_MODE';
    public const ON_CLICK_BUTTON_STYLE = 'PAYEYE_ON_CLICK_BUTTON_STYLE';

    /**
     * @return string[]
     */
    public static function getUninstallFields(): array
    {
        $reflectionClass = new \ReflectionClass(ConfigurationField::class);
        return $reflectionClass->getConstants();
    }
    /**
     * @return string[]
     */
    public static function getUiFields(): array
    {
        return [
            static::WIDGET_MODE,
            static::WIDGET_UI_BOTTOM,
            static::WIDGET_UI_SIDE_POSITION,
            static::WIDGET_UI_SIDE,
            static::WIDGET_UI_ZINDEX,
            static::ON_CLICK_BUTTON_STYLE,
            static::WIDGET_UI_MOBILE_OPEN,
        ];
    }
}
