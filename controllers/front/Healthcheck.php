<?php

declare(strict_types=1);

use PrestaShop\Module\PayEye\Controller\FrontController;

defined('_PS_VERSION_') || exit;

class PayEyeHealthcheckModuleFrontController extends FrontController
{
    public function postProcess()
    {
        $this->exitWithResponse(['status' => 'Up']);
    }
}
