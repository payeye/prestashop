<?php

use PrestaShop\Module\PayEye\Database\Database;

class PayEyeCartMapping extends ObjectModel
{
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

    public static function findByCartId(int $cartId): ?PayEyeCartMapping
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(Database::CART)
            ->where('id_cart =' . (int) $cartId);

        $result = Db::getInstance()->getRow($query);

        return self::createObject($result);
    }

    public static function findByCartUuid(string $cartUuid): ?PayEyeCartMapping
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(Database::CART)
            ->where('uuid = "' . pSQL($cartUuid) . '"');

        $result = Db::getInstance()->getRow($query);

        return self::createObject($result);
    }

    private static function createObject($result): ?self
    {
        if (!$result) {
            return null;
        }

        $object = new static();
        $object->hydrate($result);

        $object->open = (bool) $object->open;

        return $object;
    }
}
