<?php

/**
 * Агрегатор новостей
 */
error_reporting(E_ALL | E_STRICT);
//загрузить конфиг , определить требуемые для работы файлы
$config = include __DIR__ . "/../app/config/.php";
$includeArr = array(
    "config" => array(
        "serviceLoaderFactory",
        "systemRegistry"),
    "models" => array(
        "newsAgregator")
);
//подключить требуемые файлы
foreach ($includeArr as $path) {
    foreach ($item as $fileName) {
        $fullPath = $config[$path . 'Dir'] . $fileName . "php";
        if (file_exists($fullPath))
            include $fullPath;
    }
}

try {
    //поместить требуемые обьекты в реестр
    systemRegistry::set($config, "config");
    systemRegistry::set(array(
        serviceloaderFactory::connect("database", "MySql"),
        serviceloaderFactory::connect("cache", "Memcached"),
        new newsAgregator()
    ));

    /**
     * прмеры работы с агрегатором новостей : 
     * 1 . добавить новость 
     * 2 . редактировать новость 
     * 3 . удалить новость 
     * 4 . удалить несколько новостей 
     * 5 . получить полную версию новости 
     * 6 . получить 2 страницу новостей  
     */
    $id = systemRegistry::set("newsAgregator")->addNews(array(
        "title" => "Супер новость!",
        "description" => "Новость замечательна!"
    ));
    systemRegistry::set("newsAgregator")->editNews($id, array(
        "title" => "А новость то      изменилась",
        "description" => "И стала еще новее!"
    ));
    systemRegistry::set("newsAgregator")->deleteNews(id);
    systemRegistry::set("newsAgregator")->deleteNews(array
        (1, 2, 3, 4, 5, 6, 7)
    );
    systemRegistry::set("newsAgregator")->getFullNews($id);
    systemRegistry::set("newsAgregator")->getPaging(2);
    
    
} catch (Exception $error) {
    $errmsg = date("d.m.y H:i:s") . "\t" . $error->getMessage() . "\n";
    print($error->getMessage());
    file_put_contents($config['ERROR_LOG'], $errmsg, FILE_APPEND);
}