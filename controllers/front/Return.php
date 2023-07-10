<?php

declare(strict_types=1);

use PayEye\Lib\Enum\ReturnStatus;
use PayEye\Lib\Exception\OrderNotFoundException;
use PayEye\Lib\Model\RefundProduct;
use PayEye\Lib\Returns\ReturnCreateRequestModel;
use PayEye\Lib\Returns\ReturnCreateResponseModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

defined('_PS_VERSION_') || exit;

class PayEyeReturnModuleFrontController extends FrontController
{
    /** @var PayEye */
    public $module;

    public function postProcess()
    {
        if ($this->isRequestMethod('POST') === false) {
            $this->exitWithResponse(null, 404);
        }

        try {
            $handleRequest = $this->getRequest();
            $this->checkPermission($handleRequest);

            $requestModel = ReturnCreateRequestModel::createFromArray($handleRequest);

            $order = new Order($requestModel->orderId);

            if ($order->id === null || $order->module !== $this->module->name) {
                throw new OrderNotFoundException();
            }

            $state = $order->getCurrentOrderState();

            if ($state && (bool) $state->paid === false) {
                throw new Exception('Order is not paid', 400);
            }

            $returnStatus = $this->module->orderStatuses->getReturnRequest();
            $createdReturn = $this->createReturn($requestModel);

            $orderHistory = new OrderHistory();
            $orderHistory->id_order = (int) $order->id;
            $orderHistory->changeIdOrderState($returnStatus, $order->id);
            $orderHistory->add();

            $order->current_state = $returnStatus;
            $order->save();

            $response = ReturnCreateResponseModel::builder()
                ->setReturnId((string) $createdReturn->id)
                ->setStatus(ReturnStatus::CREATED);

            $this->exitWithResponse($response->toArray());
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    /**
     * @throws Exception
     */
    private function createReturn(ReturnCreateRequestModel $requestModel): PayEyeOrderReturn
    {
        $return = new PayEyeOrderReturn();
        $return->id_order = $requestModel->orderId;
        $return->status = ReturnStatus::CREATED;
        $return->currency = $requestModel->currency;

        $products = array_map(static function (RefundProduct $product) {
            $return = new PayEyeOrderReturnProduct();
            $return->id_product = $product->id;
            $return->id_product_attribute = $product->variantId;
            $return->quantity = $product->quantity;

            return $return;
        }, $requestModel->products);

        return $return->saveWithProducts($return, $products);
    }
}
