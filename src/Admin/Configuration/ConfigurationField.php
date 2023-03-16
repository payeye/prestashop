<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class ConfigurationField
{
    public const SHOP_ID = 'PAYEYE_SHOP_ID';
    public const PUBLIC_KEY = 'PAYEYE_PUBLIC_KEY';
    public const PRIVATE_KEY = 'PAYEYE_PRIVATE_KEY';
    public const ADMIN_TEST_MODE = 'PAYEYE_ADMIN_TEST_MODE';
    public const SHIPPING_MATCHING = 'PAYEYE_SHIPPING_MATCHING';

    public const PAYMENT_STATUS_WAITING = 'PAYEYE_PAYMENT_STATUS_WAITING';

    /**
     * @return string[]
     */
    public static function getUninstallFields(): array
    {
        return [
            self::SHOP_ID,
            self::PUBLIC_KEY,
            self::PRIVATE_KEY,
            self::ADMIN_TEST_MODE,
            self::SHIPPING_MATCHING,
        ];
    }
}
