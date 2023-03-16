<?php

namespace PayEye\Tests\PromoCode;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\HttpStatus;
use PayEye\Lib\PromoCode\PromoCodeRequestModel;
use PayEye\Tests\Shared\BaseTestCase;

class PromoCodeTest extends BaseTestCase
{
    private const PROMO_CODE = 'LNQX43RP'; // from database

    public function testApplyPromoCode(): void
    {
        $this->getCart($this->mock);

        $this->applyPromoCode(
            PromoCodeRequestModel::builder()
                ->setCartId($this->cartId)
                ->setPromoCode(self::PROMO_CODE)
        );

        $this->assertStatusCode(HttpStatus::OK);

        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertTrue(count($cart->promoCodes) >= 1, 'PromoCodes is empty');
    }

    public function testDeletePromoCode(): void
    {
        $this->getCart($this->mock);

        $this->applyPromoCode(
            PromoCodeRequestModel::builder()
                ->setCartId($this->cartId)
                ->setPromoCode(self::PROMO_CODE)
        );

        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertTrue(count($cart->promoCodes) >= 1, 'PromoCodes is empty');

        $this->deletePromoCode(
            PromoCodeRequestModel::builder()
                ->setCartId($this->cartId)
                ->setPromoCode(self::PROMO_CODE)
        );

        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertSame(count($cart->promoCodes), 0, 'PromoCodes is not empty');
    }

    public function testFreeDeliveryPromoCode(): void
    {
        $this->getCart($this->mock);

        $this->applyPromoCode(
            PromoCodeRequestModel::builder()
                ->setCartId($this->cartId)
                ->setPromoCode(self::PROMO_CODE)
        );

        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        foreach ($cart->shippingMethods as $shippingMethod) {
            $this->assertSame($shippingMethod->cost, 0, 'Free shipping is not exists');
        }
    }
}
