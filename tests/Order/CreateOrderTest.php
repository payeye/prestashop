<?php

namespace PayEye\Tests\Order;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\PickupPointType;
use PayEye\Lib\Model\Location;
use PayEye\Lib\Model\PickupPoint;
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

    public function testCreateOrderWithPaczkomat(): void
    {
        $pickupPoint = PickupPoint::builder()
            ->setName('WRO160M,Zielińskiego 61,53-533 Wrocław')
            ->setLocation(Location::builder()->setLat(55.234234)->setLng(54.234234))
            ->setType(PickupPointType::PARCEL_LOCKER);

        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $mock['shipping']['pickupPoint'] = $pickupPoint->toArray();
        $this->createOrder($mock);

        $response = OrderResponseModel::createFromArray($this->response->getArrayResponse());
    }
}
