<?php

namespace PrestaShop\Module\PayEye\Cart\Services;

use PayEye\Lib\Enum\PromoCodeType;
use PayEye\Lib\Model\Cart as PayEyeCart;
use PayEye\Lib\Model\Product;
use PayEye\Lib\Model\ProductAttribute;
use PayEye\Lib\Model\ProductImages;
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
    
    public function getProductPrice(): int
    {
        return $this->amountService->convertFloatToInteger($this->cart->getSummaryDetails()['total_products_wt']);
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
        $total = $this->getProductPrice() + $this->getShippingAmount() - $this->getDiscount();
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
            $id = $product['id_product'];
            $variantId = $product['id_product_attribute'];
            $quantity = (int) $product['cart_quantity'];
            $fullUrl = $this->getImageUrl($product);
            $thumbnailUrl = $this->getImageUrl($product, \ImageType::getFormattedName('cart'));
            $price = $this->amountService->convertFloatToInteger($product['price_with_reduction']);
            $regularPrice = $this->amountService->convertFloatToInteger($product['price_without_reduction']);
            $total = $this->amountService->convertFloatToInteger($product['total_wt']);
            $this->regularProductsTotal += $regularPrice * $quantity;

            $attributes = $this->getAttributes((int) $product['id_product_attribute']);

            $images = ProductImages::builder()
                ->setFullUrl($fullUrl ?: null)
                ->setThumbnailUrl($thumbnailUrl ?: null);

            $products[] = Product::builder()
                ->setId($id)
                ->setVariantId($variantId)
                ->setPrice($price)
                ->setRegularPrice($regularPrice)
                ->setTotalPrice($total)
                ->setName($product['name'])
                ->setQuantity($quantity)
                ->setImages($images)
                ->setAttributes($attributes);
        }

        $this->productsTotal = $this->amountService->convertFloatToInteger($this->cart->getSummaryDetails()['total_products_wt']);

        return $products;
    }

    private function getImageUrl(array $product, string $type = null): string
    {
        $prestaProduct = new \Product($product['id_product']);
        $name = $prestaProduct->link_rewrite;
        if (is_array($name)) {
            $name = $name[$this->cart->id_lang];
        }

        return \Context::getContext()->link->getImageLink($name, $product['id_image'], $type);
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

    private function getAttributes(int $productAttribute): array
    {
        $combination = new \Combination($productAttribute);
        $attributesName = $combination->getAttributesName($this->cart->id_lang);

        $attributes = [];

        $attributeClass = $this->isPrestaShop8OrLater() ? '\ProductAttribute' : '\Attribute';

        foreach ($attributesName as $value) {

            
            $id = $value['id_attribute'];
            $attribute = new $attributeClass($id, $this->cart->id_lang);
            $group = new \AttributeGroup($attribute->id_attribute_group, $this->cart->id_lang);

            $attributes[] = ProductAttribute::builder()
                ->setId($id)
                ->setValue($attribute->name)
                ->setName($group->name);
        }

        return $attributes;
    }

    private function isPrestaShop8OrLater(): bool
    {
        return version_compare(_PS_VERSION_, '8.0.0', '>=');
    }

}
