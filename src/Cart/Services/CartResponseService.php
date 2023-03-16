<?php

namespace PrestaShop\Module\PayEye\Cart\Services;

use PayEye\Lib\Enum\PromoCodeType;
use PayEye\Lib\Model\Cart as PayEyeCart;
use PayEye\Lib\Model\Product;
use PayEye\Lib\Model\PromoCode;
use PayEye\Lib\Service\AmountService;

class CartResponseService
{
    /** @var PromoCode[] */
    public $promoCodes;

    /** @var \Cart */
    private $cart;

    /** @var AmountService */
    private $amountService;

    /** @var Product[] */
    public $products;

    /** @var int */
    public $productsTotal;

    /** @var int */
    public $regularProductsTotal;

    /** @var PayEyeCart */
    public $payeyeCart;

    public function __construct(\Cart $cart, AmountService $amountService)
    {
        $this->cart = $cart;
        $this->amountService = $amountService;
        $this->products = $this->buildProducts();
        $this->promoCodes = $this->getPromoCodes();

        $this->payeyeCart = $this->buildCart();
    }

    public function getTotal(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getSummaryDetails()['total_price']);
    }

    public function getDiscount(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getSummaryDetails()['total_discounts']);
    }

    public function getShippingAmount(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getPackageShippingCost());
    }

    private function buildCart(): PayEyeCart
    {
        $total = $this->getTotal();
        $regularTotal = $this->regularProductsTotal + $this->getShippingAmount();

        return PayEyeCart::builder()
            ->setTotal($total)
            ->setRegularTotal($regularTotal)
            ->setDiscount($this->getDiscount())
            ->setProducts($this->productsTotal)
            ->setRegularProducts($this->regularProductsTotal);
    }

    /**
     * @return Product[]
     */
    private function buildProducts(): array
    {
        $cartProducts = $this->cart->getProducts();
        $products = [];

        foreach ($cartProducts as $product) {
            $quantity = (int) $product['cart_quantity'];
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

    private function getPromoCodes(): array
    {
        $rules = $this->cart->getCartRules();

        // @TODO promoCodes PayEye code
        return array_map(function (array $context) {
            $percent = (float) $context['reduction_percent'];
            $amount = (float) $context['reduction_amount'];

            $type = $percent > $amount ? PromoCodeType::PERCENTAGE_DISCOUNT_VALUE : PromoCodeType::CONSTANT_DISCOUNT_VALUE;

            return PromoCode::builder()
                ->setCode($context['code'])
                ->setType($type)
                ->setValue($this->amountService->convertFloatToInteger(max($percent, $amount)))
                ->setFreeDelivery((bool) $context['free_shipping'])
                ->setPayeyeCode(false);
        }, $rules);
    }
}
