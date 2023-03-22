<?php

declare(strict_types=1);

namespace PayEye\Tests\Cart;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\ShippingProvider;
use PayEye\Tests\Shared\BaseTestCase;

class GetCartControllerTest extends BaseTestCase
{
    public function shippingProviderType(): array
    {
        return [
            [ShippingProvider::COURIER],
            [ShippingProvider::SELF_PICKUP],
        ];
    }

    /**
     * @dataProvider shippingProviderType
     */
    public function testGetCart(string $shippingProviderType): void
    {
        $mock = $this->mock;
        $mock['shippingProvider'] = $shippingProviderType;

        $this->getCart($mock);

        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertTrue(count($cart->shippingMethods) >= 1, 'Shipping methods not exists');

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

        $this->assertSame($shippingProviderType, $shipping->type);
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
            $this->getCart($mock);

            $response = CartResponseModel::createFromArray($this->response->getArrayResponse());
            $this->assertSame($value->getCarrierId(), $response->shippingId);
        }
    }

    public function testGetCartWhenShippingNotExistsForSpecifyCountry(): void
    {
        $mock = $this->mock;
        $mock['shipping']['address']['postCode'] = '53-126';
        $mock['shipping']['address']['country'] = 'DE';
        $this->getCart($mock);

        $response = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertNull($response->shippingId, 'shippingId must by NULL when shipping not exists');
    }

    public function testGetCartWhenShippingAndBillingIsNull(): void
    {
        $mock = $this->mock;
        $mock['shipping'] = null;
        $mock['billing'] = null;

        $this->getCart($mock);

        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $this->assertSame($cart->shippingMethods, []);

        $productsPrice = 0;
        $regularProductsPrice = 0;

        foreach ($cart->products as $product) {
            $productsPrice += $product->price * $product->quantity;
            $regularProductsPrice += $product->regularPrice * $product->quantity;
        }

        $this->assertSame($cart->cart->products, $productsPrice, 'Price products not the same with Product price');
        $this->assertSame($cart->cart->regularProducts, $regularProductsPrice, 'Regular price products not the same with Product price');
    }
}
