<?php

namespace PayEye\Tests\Order;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\OrderStatus;
use PayEye\Lib\Order\OrderResponseModel;
use PayEye\Lib\Order\OrderUpdateStatusRequestModel;
use PayEye\Tests\Shared\BaseTestCase;

class UpdateOrderStatusTest extends BaseTestCase
{
    public function orderStatuses(): array
    {
        return [
            [OrderStatus::SUCCESS],
            [OrderStatus::REJECTED],
        ];
    }

    /**
     * @dataProvider orderStatuses
     */
    public function testUpdateOrderStatus(string $status): void
    {
        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $this->createOrder($mock);

        $response = OrderResponseModel::createFromArray($this->response->getArrayResponse());

        $request = OrderUpdateStatusRequestModel::builder()
            ->setOrderId($response->orderId)
            ->setStatus($status);

        $this->updateOrderStatus($request->toArray());

        $order = new \Order((int) $response->orderId);
        $orderStatuses = $this->module->orderStatuses;

        $this->assertSame(
            $status === OrderStatus::SUCCESS
                ? $orderStatuses->getSuccess()
                : $orderStatuses->getRejected(),
            (int) $order->current_state,
            'Order status after updated order is not correct'
        );
    }
}
