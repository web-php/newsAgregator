<?php 
/**
 * Подключится к MySQl череp драйвер PDO
 */
class db extends PDO {

    public function __construct($config)
    {
        parent::__construct(
                'mysql:host=' . $config['host'] . ';port='.$config['port'].';dbname=' . $config['dbname'], $config['username'], $config['password'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION 
                )
        );
    }

}