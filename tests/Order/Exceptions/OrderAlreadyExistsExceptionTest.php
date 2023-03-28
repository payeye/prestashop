<?php

namespace PayEye\Tests\Order\Exceptions;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Exception\OrderAlreadyExistsException;
use PayEye\Tests\Shared\BaseTestCase;

class OrderAlreadyExistsExceptionTest extends BaseTestCase
{
    public function testOrderAlreadyExistsException(): void
    {
        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $this->createOrder($mock);

        $this->createOrder($mock);
        $this->assertPayEyeException(new OrderAlreadyExistsException());
    }
}
