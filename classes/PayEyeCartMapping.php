<?php

use PrestaShop\Module\PayEye\Database\Database;
use PrestaShop\Module\PayEye\Entity\PayEyeCartMappingEntity;

class PayEyeCartMapping extends ObjectModel
{
    public $id;

    /** @var string */
    public $uuid;

    /** @var int */
    public $id_cart;

    /** @var bool */
    public $open;

    public static $definition = [
        'table' => Database::CART,
        'primary' => 'id_' . Database::CART,
        'fields' => [
            'uuid' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'id_cart' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'open' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ],
        ],
    ];

    public function setEntity(PayEyeCartMappingEntity $entity): self
    {
        $this->id = $entity->id;
        $this->uuid = $entity->uuid;
        $this->id_cart = $entity->id_cart;
        $this->open = $entity->open;

        return $this;
    }

    public static function findByCartId(int $cartId): ?PayEyeCartMappingEntity
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(Database::CART)
            ->where('id_cart =' . (int) $cartId);

        $result = Db::getInstance()->getRow($query);

        if (!$result) {
            return null;
        }

        return self::buildEntity($result);
    }

    public static function findByCartUuid(string $cartUuid): ?PayEyeCartMappingEntity
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(Database::CART)
            ->where('uuid = "' . pSQL($cartUuid) . '"');

        $result = Db::getInstance()->getRow($query);

        if (!$result) {
            return null;
        }

        return self::buildEntity($result);
    }

    private static function buildEntity(array $result): PayEyeCartMappingEntity
    {
        return PayEyeCartMappingEntity::builder()
            ->setId($result[self::$definition['primary']])
            ->setOpen($result['open'])
            ->setCartId($result['id_cart'])
            ->setUuid($result['uuid']);
    }
}
