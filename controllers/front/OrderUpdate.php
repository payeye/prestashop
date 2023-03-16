<?php

declare(strict_types=1);

use PayEye\Lib\Enum\OrderStatus;
use PayEye\Lib\Exception\OrderNotFoundException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Order\OrderUpdateStatusRequestModel;
use PayEye\Lib\Order\OrderUpdateStatusResponseModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

defined('_PS_VERSION_') || exit;

class PayEyeOrderUpdateModuleFrontController extends FrontController
{
    public function postProcess()
    {
        try {
            $request = $this->getRequest();
            $this->checkPermission($request);
            $this->exitWithResponse($this->update(OrderUpdateStatusRequestModel::createFromArray($request))->toArray());
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    /**
     * @throws OrderNotFoundException
     * @throws PrestaShopException
     */
    private function update(OrderUpdateStatusRequestModel $request): OrderUpdateStatusResponseModel
    {
        $order = new Order($request->orderId);

        if ($order->id === null) {
            throw new OrderNotFoundException();
        }

        $orderHistory = new OrderHistory();
        $orderHistory->id_order = (int) $order->id;

        switch ($request->status) {
            case OrderStatus::SUCCESS:
                $success = $this->module->orderStatuses->getSuccess();

                $order->current_state = $success;
                $orderHistory->changeIdOrderState($success, $order->id);
                break;
            case OrderStatus::REJECTED:
                $rejected = $this->module->orderStatuses->getRejected();

                $order->current_state = $rejected;
                $orderHistory->changeIdOrderState($rejected, $order->id);
                break;
        }

        $orderHistory->addWithemail();

        $order->save();

        return OrderUpdateStatusResponseModel::builder();
    }
}
