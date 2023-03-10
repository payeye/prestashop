<?php

namespace PayEye\Tests\Shared;

use Cart;
use Context;
use PayEye\Lib\Enum\ShippingType;
use PayEye\Lib\Test\TestCaseTrait;
use PayEyeCartMapping;
use PrestaShop\Module\PayEye\Entity\PayEyeCartMappingEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    use TestCaseTrait;

    /** @var string */
    private $baseUrl = 'http://payeye-prestashop.local/api-payeye/v1';

    /** @var \PayEye */
    protected $module;

    /** @var array */
    protected $mock;

    /** @var string */
    protected $cartId;

    public function setUp(): void
    {
        parent::setUp();

        $this->module = \Module::getInstanceByName('payeye');
        $this->cartId = $this->createCart()->uuid;

        $this->mock = [
            'cartId' => $this->cartId,
            "deliveryType" => ShippingType::COURIER,
            "billing" => [
                "firstName" => "Jan",
                "lastName" => "Kowalski",
                "phoneNumber" => "500 400 300",
                "email" => "bartosz.bury@payeye.com",
                "address" => [
                    "street" => "aleja Kowalska",
                    "homeNumber" => "82C",
                    "flatNumber" => "77",
                    "postCode" => "53-126",
                    "city" => "Wrocław",
                    "country" => "PL",
                ],
            ],
            "shipping" => [
                "firstName" => "Janina",
                "lastName" => "Kowalska",
                "address" => [
                    "street" => "aleja Ogryskowa",
                    "homeNumber" => "12B",
                    "flatNumber" => "1",
                    "postCode" => "53-126",
                    "city" => "Wrocław",
                    "country" => "PL",
                ],
                "pickupPoint" => null,
            ],
            "hasInvoice" => false,
        ];
    }

    public function cartRequest(array $payload): void
    {
        $this->post($this->baseUrl.'/carts', $payload, $this->module->authConfig);
    }

    public function createCart(): PayEyeCartMappingEntity
    {
        $cookie = new \Cookie('test');
        \Guest::setNewGuest($cookie);

        Context::getContext()->cookie = $cookie;

        $cart = new Cart();
        $cart->id_shop_group = 1;
        $cart->id_lang = 1;
        $cart->id_currency = 1;
        $cart->delivery_option = ' ';
        $cart->id_guest = $cookie->id_guest;
        $cart->secure_key = ' ';

        Context::getContext()->cart = $cart;

        $id_product = 1;
        $quantity = 3;

        $cart->add();

        $cart->updateQty($quantity, $id_product);

        return PayEyeCartMapping::findByCartId($cart->id);
    }
}
