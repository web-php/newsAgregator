<?php

return array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'newsAgregator',
        'port' => '3310'
    ),
    
    'cache' => array(
        'adapter' => 'Memcache',
        'host' => 'localhost',
        'port' => '11311',
    ),
    
    'application' => array(
        'configDir' => __DIR__ . '/../../app/config/',
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir' => __DIR__ . '/../../app/models/',
        'viewsDir' => __DIR__ . '/../../app/views/',
        'libraryDir' => __DIR__ . '/../../app/library/',
    ),
    
    'service' => array(
        'MySql' => array(
            'src' => __DIR__ . '/../../cfg/mysqlConnect.php',
            'class' => 'mysqlConnect'),
        'PostgreSql' => array(
            'src' => __DIR__ . '/../../cfg/postgreConnect.php',
            'class' => 'postgreConnect'),
        'Memcached' => array(
            'src' => __DIR__ . '/../../cfg/memcacheConnect.php',
            'class' => 'memcacheConnect')
    )
);
