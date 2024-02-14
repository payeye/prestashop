<?php
declare(strict_types=1);
namespace PrestaShop\Module\PayEye\Cart\Services;

use PayEye\Lib\Enum\CartType;

class CartService
{
    protected $cart;
    protected $cartType;
    protected $products = [];

    public function getCartType(): string
    {
        return $this->cartType;
    }
    public function getTotal()
    {
        var_dump($this->cart);
        exit;
        return $this->cart->total;
    }
    public function __construct(\Cart $cart)
    {
        $products = $cart->getProducts();
        $hasPhysicalProducts = false;
        $hasVirtualProducts = false;
        foreach ($products as $product) {
            if ($product['is_virtual']) {
                $hasVirtualProducts = true;
            } else {
                $hasPhysicalProducts = true;
            }
        }

        if ($hasPhysicalProducts && $hasVirtualProducts) {
            $cartType = CartType::MIXED;
        } elseif ($hasVirtualProducts) {
            $cartType = CartType::VIRTUAL;
        } else {
            $cartType = CartType::STANDARD;
        }
        $this->cartType = $cartType;
    }
}