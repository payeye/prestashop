<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Services;

defined('_PS_VERSION_') || exit;

class AuthConfig
{
    /** @var string */
    private $shopId;

    /** @var string */
    private $publicKey;

    /** @var string */
    private $secretKey;

    public function __construct(string $shopId, string $apiKey, string $secretKey)
    {
        $this->shopId = $shopId;
        $this->publicKey = $apiKey;
        $this->secretKey = $secretKey;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
