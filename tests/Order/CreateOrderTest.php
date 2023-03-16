<?php

namespace PayEye\Tests\Order;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Order\OrderResponseModel;
use PayEye\Tests\Shared\BaseTestCase;

class CreateOrderTest extends BaseTestCase
{
    public function testCreateOrder(): void
    {
        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $this->createOrder($mock);

        $response = OrderResponseModel::createFromArray($this->response->getArrayResponse());
        $order = new \Order((int) $response->orderId);
        $isoCurrency = 3;

        $this->assertIsString($response->orderId);
        $this->assertNotEmpty($response->checkoutUrl);
        $this->assertIsInt($response->totalAmount);
        $this->assertIsInt($response->cartAmount);
        $this->assertIsInt($response->shippingAmount);
        $this->assertSame($response->totalAmount, $response->cartAmount + $response->shippingAmount);
        $this->assertSame(strlen($response->currency), $isoCurrency);
        $this->assertIsString($response->checkoutUrl);
        $this->assertSame($this->module->orderStatuses->getCreated(), (int) $order->current_state, 'Order status after created order is not correct');
    }
}
