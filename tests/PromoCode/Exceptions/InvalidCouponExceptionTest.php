<?php

namespace PayEye\Tests\PromoCode\Exceptions;

use PayEye\Lib\Exception\InvalidCouponException;
use PayEye\Lib\PromoCode\PromoCodeRequestModel;
use PayEye\Tests\Shared\BaseTestCase;

class InvalidCouponExceptionTest extends BaseTestCase
{
    public function testInvalidCouponException(): void
    {
        $this->getCart($this->mock);

        $this->applyPromoCode(
            PromoCodeRequestModel::builder()
                ->setCartId($this->cartId)
                ->setPromoCode('invalid-coupon')
        );

        $this->assertPayEyeException(new InvalidCouponException());
    }
}
