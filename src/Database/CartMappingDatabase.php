<?php

namespace PrestaShop\Module\PayEye\Database;

class CartMappingDatabase
{
    /** @var string */
    private $tableName = _DB_PREFIX_ . 'payeye_cart_mapping';

    /** @var string */
    private $engine = _MYSQL_ENGINE_;

    public function createTable(): bool
    {
        $sql = "CREATE TABLE IF NOT EXISTS $this->tableName (
              id_payeye_cart_mapping BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              id_cart int unsigned NOT NULL,
              uuid varchar(36) NOT NULL UNIQUE,
              open TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (id_payeye_cart_mapping)
            ) ENGINE=$this->engine DEFAULT CHARSET=UTF8;
        ";

        return \Db::getInstance()->execute($sql);
    }
}
