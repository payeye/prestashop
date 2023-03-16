<?php

namespace PayEye\Tests\Shared;

use PayEye\Lib\Enum\ShippingProvider;
use PayEye\Lib\PromoCode\PromoCodeRequestModel;
use PayEye\Lib\Test\TestCaseTrait;
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

    /** @var PayEyeCartMappingEntity */
    protected $cartMapping;

    public function setUp(): void
    {
        parent::setUp();

        $this->module = \Module::getInstanceByName('payeye');
        $this->cartMapping = $this->createMockCart();
        $this->cartId = $this->cartMapping->uuid;

        $this->mock = [
            'cartId' => $this->cartId,
            'shippingProvider' => ShippingProvider::COURIER,
            'billing' => [
                'firstName' => 'Jan',
                'lastName' => 'Kowalski',
                'phoneNumber' => '500 400 300',
                'email' => 'bartosz.bury@payeye.com',
                'address' => [
                    'street' => 'aleja Kowalska',
                    'buildingNumber' => '82C',
                    'flatNumber' => '77',
                    'postCode' => '53-126',
                    'city' => 'Wrocław',
                    'country' => 'PL',
                ],
            ],
            'shipping' => [
                'firstName' => 'Janina',
                'lastName' => 'Kowalska',
                'address' => [
                    'street' => 'aleja Ogryskowa',
                    'buildingNumber' => '12B',
                    'flatNumber' => '1',
                    'postCode' => '53-126',
                    'city' => 'Wrocław',
                    'country' => 'PL',
                ],
                'pickupPoint' => null,
            ],
            'hasInvoice' => false,
            'invoice' => null,
        ];
    }

    public function deletePromoCode(PromoCodeRequestModel $requestModel): void
    {
        $this->delete($this->baseUrl . '/carts/promo-codes', array_merge($this->addSignature($this->module->authConfig), $requestModel->toArray()));
    }

    public function applyPromoCode(PromoCodeRequestModel $requestModel): void
    {
        $this->post($this->baseUrl . '/carts/promo-codes', array_merge($this->addSignature($this->module->authConfig), $requestModel->toArray()));
    }

    public function getCart(array $payload): void
    {
        $this->post($this->baseUrl . '/carts', array_merge($this->addSignature($this->module->authConfig), $payload));
    }

    public function createOrder(array $payload): void
    {
        $this->post($this->baseUrl . '/orders', array_merge($this->addSignature($this->module->authConfig), $payload));
    }

    public function updateOrderStatus(array $payload): void
    {
        $this->put($this->baseUrl . '/orders/status', array_merge($this->addSignature($this->module->authConfig), $payload));
    }

    public function createMockCart(): PayEyeCartMappingEntity
    {
        $cookie = new \Cookie('test');
        \Guest::setNewGuest($cookie);

        \Context::getContext()->cookie = $cookie;

        $cart = new \Cart();
        $cart->id_shop_group = 1;
        $cart->id_lang = 1;
        $cart->id_currency = 1;
        $cart->delivery_option = ' ';
        $cart->id_guest = $cookie->id_guest;
        $cart->secure_key = ' ';

        \Context::getContext()->cart = $cart;

        $id_product = 1;
        $quantity = 3;

        $cart->add();

        $cart->updateQty($quantity, $id_product);

        return \PayEyeCartMapping::findByCartId($cart->id);
    }
}
