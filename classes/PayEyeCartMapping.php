<?php

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
        'table' => 'payeye_cart_mapping',
        'primary' => 'id_payeye_cart_mapping',
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
            ->from('payeye_cart_mapping')
            ->where('id_cart =' . (int) $cartId);

        $result = Db::getInstance()->getRow($query);

        if (!$result) {
            return null;
        }

        return PayEyeCartMappingEntity::builder()
            ->setId($result['id_payeye_cart_mapping'])
            ->setOpen($result['open'])
            ->setCartId($result['id_cart'])
            ->setUuid($result['uuid']);
    }
}
