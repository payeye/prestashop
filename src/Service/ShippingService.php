<?php

namespace PrestaShop\Module\PayEye\Service;

use PayEye\Lib\Model\ShippingMethod;
use PayEye\Lib\Service\AmountService;

class ShippingService
{
    /** @var ShippingMethod[] */
    private $shippingMethods = [];

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

    /**
     * @return ShippingMethod[]
     */
    public function getShippingMethods(): array
    {
        return $this->shippingMethods;
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

        return $shipping[array_key_first($shipping)];
    }

    private function availableShippingMethods(): void
    {
        $shippingMatchCollection = $this->payeye->shippingMatchCollection;

        foreach ($this->deliveryOptions as $deliveryOption) {
            $findByCarrier = $shippingMatchCollection->findByCarrierId($deliveryOption['id']);

            if ($findByCarrier === null || empty($findByCarrier->getProvider())) {
                continue;
            }

            $regularCost = $this->amountService->convertFloatToInteger($deliveryOption['price_with_tax']);

            $this->shippingMethods[] = ShippingMethod::builder()
                ->setId($deliveryOption['id'])
                ->setLabel($deliveryOption['name'])
                ->setCost($regularCost)
                ->setRegularCost($regularCost)
                ->setType($findByCarrier->getProvider());
        }
    }
}
