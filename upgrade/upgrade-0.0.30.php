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

function upgrade_module_0_0_30($module)
{
    $module->registerHook('moduleRoutes');
    $module->registerHook('actionFrontControllerSetMedia');

    return true;
}
