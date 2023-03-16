<?php

namespace PayEye\Tests\Cart\Exceptions;

use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Tests\Shared\BaseTestCase;

class CartNotFoundExceptionTest extends BaseTestCase
{
    public function testCartNotFoundException(): void
    {
        $this->getCart([
            'cartId' => 'card-id-not-exists',
        ]);

        $this->assertPayEyeException(new CartNotFoundException());
    }
}
