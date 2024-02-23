<?php

namespace PrestaShop\Module\PayEye\Shared;

class FilterService
{
    public static function filterPromoCodes(array $promoCodes): array
    {
        $filteredPromoCodes = \Hook::exec('actionFilterPayeyePromoCodes', ['promoCodes' => $promoCodes], null, true);
        if (is_array($filteredPromoCodes)) {
            foreach ($filteredPromoCodes as $pluginName => $value) {
                $promoCodes = $value;
            }
        }
        return $promoCodes;
    }
}