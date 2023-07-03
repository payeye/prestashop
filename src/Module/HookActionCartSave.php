<?php

namespace PrestaShop\Module\PayEye\Module;

use PayEye\Lib\Auth\AuthService;
use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\HttpClient\Enum\EvenType;
use PayEye\Lib\HttpClient\Exception\HttpException;
use PayEye\Lib\HttpClient\Model\RefreshCartRequest;
use PayEye\Lib\HttpClient\PayEyeHttpClient;
use PayEye\Lib\Tool\Uuid;

class HookActionCartSave
{
    /** @var \PayEye */
    public $module;

    public function __construct(\PayEye $module)
    {
        $this->module = $module;
    }

    public function __invoke(array $payload)
    {
        try {
            /** @var \Cart $cart */
            $cart = $payload['cart'];
            $cartId = $cart->id;

            if ($cartId === null) {
                return;
            }

            $cartMapping = \PayEyeCartMapping::findByCartId($cartId);

            if ($cartMapping && $cartMapping->open) {
                //$this->silentPush($cartMapping);
            }

            if ($cartMapping) {
                return;
            }

            $cartMapping = new \PayEyeCartMapping();
            $cartMapping->id_cart = $cart->id;
            $cartMapping->open = false;
            $cartMapping->uuid = Uuid::generate();

            $cartMapping->add();
        } catch (\PrestaShopDatabaseException|\PrestaShopException|HttpException $e) {
            // do nothing
        }
    }

    /**
     * @throws HttpException
     */
    private function silentPush(\PayEyeCartMapping $cartMapping): void
    {
        $httpClient = new PayEyeHttpClient($this->module->config);

        $request = new RefreshCartRequest(
            $cartMapping->uuid,
            $this->module->authConfig->getShopId(),
            EvenType::CART_CHANGED
        );

        $auth = new AuthService(
            new HashService($this->module->authConfig),
            SignatureFrom::REFRESH_CART_REQUEST,
            $request->toArray()
        );

        $httpClient->refreshCart($request, $auth);
    }
}
