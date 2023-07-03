<?php

use PrestaShop\Module\PayEye\Database\Database;

class PayEyeOrderReturn extends ObjectModel
{
    /** @var int */
    public $id_order;

    /** @var float|null */
    public $amount;

    /** @var string */
    public $currency;

    /** @var string */
    public $status;

    public $date_add;

    public $date_upd;

    public static $definition = [
        'table' => Database::RETURN,
        'primary' => 'id_' . Database::RETURN,
        'fields' => [
            'id_order' => [
                'type' => self::TYPE_INT,
                'required' => true,
            ],
            'currency' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'amount' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
            ],
            'status' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'],
        ],
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * @return PayEyeOrderReturnProduct[]
     */
    public function getProducts(): array
    {
        return PayEyeOrderReturnProduct::getByField('id_' . Database::RETURN, $this->id);
    }

    /**
     * @param PayEyeOrderReturn $orderReturn
     * @param PayEyeOrderReturnProduct[] $products
     *
     * @return void
     *
     * @throws Exception
     */
    public function saveWithProducts(PayEyeOrderReturn $orderReturn, array $products): PayEyeOrderReturn
    {
        Db::getInstance()->execute('START TRANSACTION');

        try {
            $orderReturn->save();

            foreach ($products as $product) {
                $product->id_order_payeye_return = $orderReturn->id;
                $product->save();
            }

            Db::getInstance()->execute('COMMIT');

            return $orderReturn;
        } catch (Exception $exception) {
            Db::getInstance()->execute('ROLLBACK');

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $id
     *
     * @return PayEyeOrderReturn[]
     *
     * @throws PrestaShopDatabaseException
     */
    public static function findByOrderId(int $id): array
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(Database::RETURN)
            ->where('id_order =' . (int) $id);

        $results = Db::getInstance()->executeS($query);

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
