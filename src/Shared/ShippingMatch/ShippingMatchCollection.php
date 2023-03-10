<?php

namespace PrestaShop\Module\PayEye\Shared\ShippingMatch;

use PrestaShop\Module\PayEye\Shared\Collection;

class ShippingMatchCollection extends Collection
{
    public function findByCarrierId(string $carrierId): ?ShippingMatch
    {
        $this->findBy('carrierId', $carrierId);

        return $this->returnNullOrObject();
    }

    public function findByProvider(string $provider): ?ShippingMatch
    {
        $this->findBy('provider', $provider);

        return $this->returnNullOrObject();
    }

    private function returnNullOrObject(): ?ShippingMatch
    {
        return empty($this->array) ? null : ShippingMatch::buildFromArray($this->array);
    }

    /**
     * @return ShippingMatch[]
     */
    public function getCopyObject(): array
    {
        return array_map(static function (array $context) {
            return ShippingMatch::buildFromArray($context);
        }, $this->copyArray);
    }
}
