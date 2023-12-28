<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Shared;

class Collection
{
    /** @var array */
    protected $array;

    /** @var array */
    protected $copyArray;

    public function __construct(array $array)
    {
        $this->array = $array;
        $this->copyArray = $array;
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function getCopyArray(): array
    {
        return $this->copyArray;
    }

    protected function findBy(string $key, $value): self
    {
        $this->array = array_filter($this->copyArray, static function ($context) use ($key, $value) {
            return $context[$key] === $value;
        });

        if ($this->array) {
            if (version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                $keys = array_keys($this->array);
                $firstKey = reset($keys);
                $this->array = $this->array[$firstKey];
            } else {
                $this->array = $this->array[array_key_first($this->array)];
            }
        }

        return $this;
    }
}
