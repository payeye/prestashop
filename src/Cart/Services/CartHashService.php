<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Cart\Services;

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Cart\CartResponseModel;

class CartHashService
{
    /** @var AuthConfig */
    private $authConfig;

    public function __construct(AuthConfig $authConfig)
    {
        $this->authConfig = $authConfig;
    }

    public function calculateCartHash(CartResponseModel $cart): string
    {
        return (HashService::create($this->authConfig))->cartHash(
            $cart->promoCodes,
            $cart->shippingMethods,
            $cart->cart,
            $cart->shippingId,
            $cart->currency,
            $cart->products
        );
    }
}
