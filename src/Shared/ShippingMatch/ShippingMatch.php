<?php

namespace PrestaShop\Module\PayEye\Shared\ShippingMatch;

use PayEye\Lib\Tool\Builder;

class ShippingMatch
{
    use Builder;

    /** @var string */
    private $carrierId;

    /** @var string */
    private $provider;

    public static function buildFromArray(array $context): self
    {
        $self = self::builder();
        $self->carrierId = $context['carrierId'] ?? '';
        $self->provider = $context['provider'] ?? '';

        return $self;
    }

    public function getCarrierId(): string
    {
        return $this->carrierId;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
