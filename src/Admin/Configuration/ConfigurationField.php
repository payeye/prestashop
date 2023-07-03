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
    public const SHIPPING_MATCHING = 'PAYEYE_SHIPPING_MATCHING';

    public const PAYMENT_STATUS_WAITING = 'PAYEYE_PAYMENT_STATUS_WAITING';
    public const RETURN_REQUEST = 'PAYEYE_RETURN_REQUEST';

    /**
     * @return string[]
     */
    public static function getUninstallFields(): array
    {
        return [
            self::TEST_MODE,
            self::SHOP_ID,
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            self::SHIPPING_MATCHING,
            self::WIDGET_UI_BOTTOM,
        ];
    }
}
