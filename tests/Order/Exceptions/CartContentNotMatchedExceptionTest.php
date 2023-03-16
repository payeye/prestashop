<?php

namespace PayEye\Tests\Order\Exceptions;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Exception\CartContentNotMatchedException;
use PayEye\Tests\Shared\BaseTestCase;

class CartContentNotMatchedExceptionTest extends BaseTestCase
{
    public function case(): array
    {
        return [
            ['CART_UPDATED'],
            ['PRODUCT_PRICE_CHANGED'],
        ];
    }

    /**
     * @dataProvider case
     */
    public function testCartContentNotMatchedException(string $case): void
    {
        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;

        switch ($case) {
            case 'CART_UPDATED':
                $currentCart = new \Cart($this->cartMapping->id_cart);
                $currentCart->updateQty(1, $cart->products[0]->id);
                break;
            case 'PRODUCT_PRICE_CHANGED':
                $product = new \Product($cart->products[0]->id);
                $product->price = $product->price += 0.01;
                $product->save();
                break;
        }

        $this->createOrder($mock);

        $this->assertPayEyeException(new CartContentNotMatchedException());
    }
}
