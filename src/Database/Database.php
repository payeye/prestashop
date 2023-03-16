<?php

namespace PrestaShop\Module\PayEye\Database;

class Database
{
    public const CART = 'cart_payeye_mapping';

    /** @var string */
    private $prefix = _DB_PREFIX_;

    /** @var string */
    private $engine = _MYSQL_ENGINE_;

    public function createTable(): bool
    {
        return $this->createCartMapping();
    }

    private function createCartMapping(): bool
    {
        $table = $this->prefix . self::CART;

        $sql = "CREATE TABLE IF NOT EXISTS $table (
              id_cart_payeye_mapping BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              id_cart int unsigned NOT NULL,
              uuid varchar(36) NOT NULL UNIQUE,
              open TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (id_cart_payeye_mapping)
            ) ENGINE=$this->engine DEFAULT CHARSET=UTF8;
        ";

        return \Db::getInstance()->execute($sql);
    }
}
