<?php

namespace PrestaShop\Module\PayEye\Entity;

class PayEyeCartMappingEntity
{
    /** @var string|null */
    public $id;

    /** @var string */
    public $uuid;

    /** @var int */
    public $id_cart;

    /** @var bool */
    public $open;

    public static function builder(): self
    {
        return new self();
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function setCartId(int $id_cart): self
    {
        $this->id_cart = $id_cart;

        return $this;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }
}
