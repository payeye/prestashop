<?php

use PrestaShop\Module\PayEye\Database\Database;

class PayEyeOrderReturnProduct extends ObjectModel
{
    /** @var int */
    public $id_order_payeye_return;

    /** @var int */
    public $id_product;

    /** @var int */
    public $id_product_attribute;

    /** @var string */
    public $quantity;

    /** @var string */
    public $date_add;

    public static $definition = [
        'table' => Database::RETURN_PRODUCT,
        'primary' => 'id_' . Database::RETURN_PRODUCT,
        'fields' => [
            'id_order_payeye_return' => [
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt',
            ],
            'id_product' => [
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt',
            ],
            'id_product_attribute' => [
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt',
            ],
            'quantity' => [
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt',
            ],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'],
        ],
    ];

    public static function getByField($field, $value): array
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(static::$definition['table']);
        $sql->where("`$field` = '" . pSQL($value) . "'");

        $results = Db::getInstance()->executeS($sql);

        $objects = [];
        if ($results) {
            foreach ($results as $result) {
                $object = new static();
                $object->hydrate($result);
                $objects[] = $object;
            }
        }

        return $objects;
    }
}
