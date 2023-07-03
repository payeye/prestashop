<?php

namespace PrestaShop\Module\PayEye\Database;

class Database
{
    public const CART = 'cart_payeye_mapping';
    public const RETURN = 'order_payeye_return';
    public const RETURN_PRODUCT = 'order_payeye_return_product';

    /** @var string */
    private $prefix = _DB_PREFIX_;

    /** @var string */
    private $engine = _MYSQL_ENGINE_;

    public function createTable(): bool
    {
        return
            $this->createCartMapping()
            && $this->createReturn()
            && $this->createReturnProduct();
    }

    private function createCartMapping(): bool
    {
        $table = $this->prefix . self::CART;

        $sql = "CREATE TABLE IF NOT EXISTS $table (
              id_cart_payeye_mapping BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              id_cart INT UNSIGNED NOT NULL,
              uuid varchar(36) NOT NULL UNIQUE,
              open TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (id_cart_payeye_mapping)
            ) ENGINE=$this->engine DEFAULT CHARSET=UTF8;
        ";

        return \Db::getInstance()->execute($sql);
    }

    private function createReturn(): bool
    {
        $table = $this->prefix . self::RETURN;

        $sql = "CREATE TABLE IF NOT EXISTS $table (
              id_order_payeye_return BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              id_order INT UNSIGNED NOT NULL,
              amount DECIMAL(20,2) NOT NULL,
              currency varchar(3) NOT NULL,
              status varchar(36) NOT NULL,
              date_add datetime NOT NULL,
              date_upd datetime NOT NULL,
              PRIMARY KEY (id_order_payeye_return)
            ) ENGINE=$this->engine DEFAULT CHARSET=UTF8;
        ";

        return \Db::getInstance()->execute($sql);
    }

    private function createReturnProduct(): bool
    {
        $table = $this->prefix . self::RETURN_PRODUCT;

        $sql = "CREATE TABLE IF NOT EXISTS $table (
              id_order_payeye_return_product BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              id_order_payeye_return INT UNSIGNED  NOT NULL,
              id_product INT UNSIGNED NOT NULL,
              id_product_attribute INT UNSIGNED NOT NULL,
              quantity INT UNSIGNED NOT NULL,
              date_add datetime NOT NULL,
              PRIMARY KEY (id_order_payeye_return_product)
            ) ENGINE=$this->engine DEFAULT CHARSET=UTF8;
        ";

        return \Db::getInstance()->execute($sql);
    }
}
