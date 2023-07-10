<?php

declare(strict_types=1);

use PayEye\Lib\Enum\ReturnStatus;
use PayEye\Lib\Enum\TransferStatus;
use PayEye\Lib\Exception\ReturnNotFoundException;
use PayEye\Lib\Returns\ReturnUpdateStatusRequestModel;
use PayEye\Lib\Returns\ReturnUpdateStatusResponseModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

defined('_PS_VERSION_') || exit;

class PayEyeReturnStatusModuleFrontController extends FrontController
{
    public function postProcess(): void
    {
        if ($this->isRequestMethod('PUT') === false) {
            $this->exitWithResponse(null, 404);
        }

        try {
            $handleRequest = $this->getRequest();
            $this->checkPermission($handleRequest);

            $requestModel = ReturnUpdateStatusRequestModel::createFromArray($handleRequest);

            $return = new PayEyeOrderReturn($requestModel->returnId);

            if ($return->id === null) {
                throw new ReturnNotFoundException();
            }

            if ($return->status !== TransferStatus::IN_PROGRESS) {
                throw new PrestaShopException('Return is not IN_PROGRESS status');
            }

            switch ($requestModel->transferStatus) {
                case TransferStatus::REJECTED:
                    $return->status = ReturnStatus::CREATED;
                    break;
                case TransferStatus::SUCCESS:
                    $return->status = TransferStatus::SUCCESS;
                    break;
            }

            $return->save();

            $this->exitWithResponse(ReturnUpdateStatusResponseModel::builder()->toArray());
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }
}
