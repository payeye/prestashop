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
        $cartRuleId = \CartRule::getIdByCode(self::PROMO_CODE);
        $beforeQuantity = (new \CartRule($cartRuleId))->quantity;

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

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $this->createOrder($mock);

        $query = new \DbQuery();
        $query
            ->select('quantity')
            ->from('cart_rule')
            ->where('id_cart_rule =' . (int) $cartRuleId);

        $quantity = \Db::getInstance()->getValue($query);

        $this->assertNotSame($beforeQuantity, $quantity);
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
