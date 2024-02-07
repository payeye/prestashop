<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Cart\Services;

use PayEye\Lib\Model\ShippingMethod;
use PayEye\Lib\Service\AmountService;

class ShippingService
{
    /** @var ShippingMethod[] */
    public $shippingMethods = [];

    /** @var array */
    private $deliveryOptions;

    /** @var AmountService */
    private $amountService;

    /** @var \PayEye */
    private $payeye;

    public function __construct(array $deliveryOptions, AmountService $amountService, \PayEye $payEye)
    {
        $this->deliveryOptions = $deliveryOptions;
        $this->amountService = $amountService;
        $this->payeye = $payEye;

        $this->availableShippingMethods();
    }

    public function getDefaultShipping(?string $shippingType): ?ShippingMethod
    {
        $shipping = array_filter($this->shippingMethods, static function (ShippingMethod $model) use ($shippingType) {
            return $model->type === $shippingType;
        });

        if (!$shipping) {
            return null;
        }

        usort($shipping, static function (ShippingMethod $current, ShippingMethod $next) {
            return $current->cost > $next->cost;
        });

        if (version_compare(_PS_VERSION_, '1.7.6', '<')) {
            $keys = array_keys($shipping);
            $firstKey = reset($keys);

            return $shipping[$firstKey];
        } else {
            return $shipping[array_key_first($shipping)];
        }
    }

    private function availableShippingMethods(): void
    {
        $shippingMatchCollection = $this->payeye->shippingMatchCollection;

        foreach ($this->deliveryOptions as $deliveryOption) {
            $findByCarrier = $shippingMatchCollection->findByCarrierId((string) $deliveryOption['id']);

            if ($findByCarrier === null || empty($findByCarrier->getProvider())) {
                continue;
            }

            $regularCost = $this->amountService->convertFloatToInteger($deliveryOption['price_with_tax']);
            $price = $regularCost;

            if ($this->isFreeShipping(\Context::getContext()->cart, $deliveryOption)) {
                $price = 0;
            }

            $this->shippingMethods[] = ShippingMethod::builder()
                ->setId((string) $deliveryOption['id'])
                ->setLabel($deliveryOption['name'])
                ->setCost($price)
                ->setRegularCost($regularCost)
                ->setType($findByCarrier->getProvider());
        }
    }

    private function isFreeShipping(\Cart $cart, array $carrier): bool
    {
        $free = false;

        if ($carrier['is_free']) {
            return true;
        }

        foreach ($cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free = true;

                break;
            }
        }

        return $free;
    }
}
