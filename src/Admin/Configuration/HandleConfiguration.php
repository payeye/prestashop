<?php

namespace PrestaShop\Module\PayEye\Admin\Configuration;

defined('_PS_VERSION_') || exit;

class HandleConfiguration
{
    public static function handleMatching(array $payload): string
    {
        $matching = [];
        foreach ($payload as $key => $value) {
            if (strpos($key, ConfigurationField::SHIPPING_MATCHING) !== false) {
                $matching[] = [
                    'carrierId' => str_replace(ConfigurationField::SHIPPING_MATCHING, '', $key),
                    'provider' => $value,
                ];
            }
        }

        return json_encode($matching);
    }
}
