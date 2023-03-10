<?php

namespace PrestaShop\Module\PayEye\Service;

use PayEye\Lib\Model\Product;
use PayEye\Lib\Service\AmountService;

class CartService
{
    /** @var \Cart */
    private $cart;

    /** @var AmountService */
    private $amountService;

    /** @var Product[] */
    private $products;

    /** @var int */
    private $productsTotal;

    /** @var int */
    private $regularProductsTotal;

    public function __construct(\Cart $cart, AmountService $amountService)
    {
        $this->cart = $cart;
        $this->amountService = $amountService;
        $this->products = $this->buildProducts();
    }


    public function getProductsTotal(): int
    {
        return $this->productsTotal;
    }

    public function getRegularProductsTotal(): int
    {
        return $this->regularProductsTotal;
    }

    public function getTotalAmount(): int
    {
        return $this->getCartAmount() + $this->getShippingAmount();
    }

    public function getCartAmount(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getSummaryDetails()['total_products_wt']);
    }

    public function getShippingAmount(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getPackageShippingCost());
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return Product[]
     */
    private function buildProducts(): array
    {
        $cartProducts = $this->cart->getProducts();
        $products = [];

        foreach ($cartProducts as $product) {
            $quantity = (int)$product['cart_quantity'];
            $imageUrl = $this->getImageUrl($product);
            $price = $this->amountService->convertFloatToInteger($product['price_with_reduction']);
            $regularPrice = $this->amountService->convertFloatToInteger($product['price_without_reduction']);
            $this->productsTotal += $price * $quantity;
            $this->regularProductsTotal += $regularPrice * $quantity;

            $products[] = Product::builder()
                ->setId($product['id_product'])
                ->setPrice($price)
                ->setRegularPrice($regularPrice)
                ->setName($product['name'])
                ->setQuantity($quantity)
                ->setImageUrl($imageUrl);
        }

        return $products;
    }

    private function getImageUrl(array $product): string
    {
        $prestaProduct = new \Product($product['id_product']);
        $image = \Image::getCover($prestaProduct->id);
        $name = $prestaProduct->link_rewrite;
        if (is_array($name)) {
            $name = $name[$this->cart->id_lang];
        }

        return \Context::getContext()->link->getImageLink($name, $image['id_image']);
    }
}
