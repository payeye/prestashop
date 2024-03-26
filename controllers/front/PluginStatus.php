<?php

declare(strict_types=1);

use PayEye\Lib\Enum\PluginEvents;
use PayEye\Lib\Enum\PluginModes;
use PayEye\Lib\Plugin\PluginStatusRequestModel;
use PayEye\Lib\Exception\PayEyePaymentException;
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
        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $this->checkPermission([
                        'signatureFrom' => SignatureFrom::PLUGIN_STATUS_REQUEST,
                        'shopIdentifier' => $this->module->authConfig->getShopId(),
                        'signature' => (string)Tools::getValue('signature')
                    ]);
                    $this->exitWithResponse($this->pluginStatus(PluginEvents::PLUGIN_INFO));
                    break;
                case 'POST':
                    $request = $this->getRequest();
                    $this->checkPermission($request);
                    $request = PluginStatusRequestModel::createFromArray($request);
                    $this->changeConfig($request);
                    $this->exitWithResponse($this->pluginStatus(PluginEvents::PLUGIN_CONFIG_CHANGED));
            }
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    protected function changeConfig(PluginStatusRequestModel $request)
    {
        $testMode = 0;
        if ($request->getPluginMode() === PluginModes::PLUGIN_MODE_INTEGRATION) {
            $testMode = 1;
        }
        Configuration::updateValue(ConfigurationField::TEST_MODE, $testMode);
    }

    /**
     * @param $pluginEvent
     * @return array
     */
    protected function pluginStatus($pluginEvent)
    {
        $pluginMode = Configuration::get(ConfigurationField::TEST_MODE) ? PluginModes::PLUGIN_MODE_INTEGRATION : PluginModes::PLUGIN_MODE_PRODUCTION;

        $response = [
            'apiVersion' => $this->module->getApiVersion(),
            'shopIdentifier' => $this->module->authConfig->getShopId(),
            'pluginMode' => $pluginMode,
            'languageVersion' => 'PHP ' . phpversion(),
            'platformVersion' => _PS_VERSION_,
            'pluginVersion' => $this->module->version,
            'pluginEvent' => $pluginEvent,
            'pluginConfig' => null,
            'signatureFrom' => SignatureFrom::PLUGIN_UPDATE_STATUS_REQUEST
        ];

        return $response;
    }
}
