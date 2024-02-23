<?php
/**
 * PayEye
 *
 * @author    PayEye
 * @copyright Copyright (c) 2023 PayEye
 * @license   http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_0($module)
{
    $module->registerHook('actionFilterPayeyePromoCodes');

    return true;
}
