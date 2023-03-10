<?php

declare(strict_types=1);

namespace PayEye\Tests\Cart\Integrations;

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\ShippingType;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\SignatureNotMatchedException;
use PayEye\Tests\Shared\BaseTestCase;

class GetCartControllerTest extends BaseTestCase
{

    public function deliveryType(): array
    {
        return [
            [ShippingType::COURIER],
            [ShippingType::SELF_PICKUP],
        ];
    }

    /**
     * @dataProvider deliveryType
     */
    public function testGetCart(string $deliveryType): void
    {
        $mock = $this->mock;
        $mock['deliveryType'] = $deliveryType;

        $this->cartRequest($mock);

        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());
        $shipping = array_filter($cart->shippingMethods, static function ($shipping) use ($cart) {
            return $shipping->id === $cart->shippingId;
        });
        $shipping = $shipping[array_key_first($shipping)];

        $productsPrice = 0;
        $regularProductsPrice = 0;

        foreach ($cart->products as $product) {
            $productsPrice += $product->price * $product->quantity;
            $regularProductsPrice += $product->regularPrice * $product->quantity;
        }

        $this->assertIsInt($cart->cart->regularProducts);
        $this->assertIsInt($cart->cart->products);

        $this->assertSame($cart->cart->products, $productsPrice, 'Price products not the same with Product price');
        $this->assertSame($cart->cart->regularProducts, $regularProductsPrice, 'Regular price products not the same with Product price');

        $this->assertSame($deliveryType, $shipping->type);
        $this->assertSame($cart->cart->regularTotal, $cart->cart->regularProducts + $shipping->cost, 'Regular price not matched');
        $this->assertSame($cart->cart->total, $cart->cart->products + $shipping->cost - $cart->cart->discount, 'Total price not matched');

        // If test not pass go to backoffice http://payeye-prestashop.local/admin008qumzki/index.php/configure/shop/preferences/preferences?_token=MVRPq_VdqmdONDpzpgpr2-WTZb1PlTDPloQVM4RZZuk and change form_price_round_type to 1

        $this->assertIsString($cart->products[0]->imageUrl, 'ImageUrl is not string');
    }

    public function testChangeShippingIdToAnother(): void
    {
        $mock = $this->mock;

        foreach ($this->module->shippingMatchCollection->getCopyObject() as $value) {
            $mock['shippingId'] = $value->getCarrierId();
            $this->cartRequest($mock);

            $response = CartResponseModel::createFromArray($this->response->getArrayResponse());
            $this->assertSame($value->getCarrierId(), $response->shippingId);
        }
    }

    public function testGetCartWhenShippingNotExistsForSpecifyCountry(): void
    {
        $mock = $this->mock;
        $mock['shipping']['address']['postCode'] = '53-126';
        $mock['shipping']['address']['country'] = 'DE';
        $this->cartRequest($mock);

        $response = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertNull($response->shippingId, 'shippingId must by NULL when shipping not exists');
    }

    public function testCartNotFoundException(): void
    {
        $this->cartRequest([
            'cartId' => 'card-id-not-exists',
        ]);

        $this->assertPayEyeException(new CartNotFoundException());
    }

    public function testSignatureNotMatchedException(): void
    {
        $this->module->authConfig = new AuthConfig('invalid-shop-id', 'invalid-api-key', 'invalid-secret-key');
        $this->cartRequest([
            'cartId' => '9e814eb4-9fe5-477f-aa96-0010ff3b4b11',
        ]);

        $this->assertPayEyeException(new SignatureNotMatchedException());
    }
}
