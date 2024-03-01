<?php

declare(strict_types=1);

use PrestaShop\Module\PayEye\Controller\FrontController;
use PrestaShop\Module\PayEye\Admin\Configuration\ConfigurationField;
use PayEye\Lib\Enum\SignatureFrom;

defined('_PS_VERSION_') || exit;

class PayEyePluginStatusModuleFrontController extends FrontController
{
    /** @var PayEye */
    public $module;

    public function postProcess()
    {
        $this->checkPermission([
            'signatureFrom' => SignatureFrom::PLUGIN_STATUS_REQUEST,
            'shopIdentifier' => $this->module->authConfig->getShopId(),
            'signature' => Tools::getValue('signature')
        ]);

        $pluginMode = Configuration::get(ConfigurationField::TEST_MODE) ? 'INTEGRATION' : 'PRODUCTION';

        $response = [
            'apiVersion' => $this->module->getApiVersion(),
            'shopIdentifier' => $this->module->authConfig->getShopId(),
            'pluginMode' => $pluginMode,
            'languageVersion' => 'PHP ' . phpversion(),
            'platformVersion' => _PS_VERSION_,
            'pluginVersion' => $this->module->version,
            'pluginEvent' => 'PLUGIN_ACTIVATED',
            'pluginConfig' => null,
            'signatureFrom' => SignatureFrom::PLUGIN_UPDATE_STATUS_REQUEST
        ];

        $this->exitWithResponse($response);
    }
}
