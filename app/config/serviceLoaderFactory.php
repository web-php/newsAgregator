<?php

/**
 * фабрика для подключения сервисов приложения 
 * @param string $groupСfg группа настроек в конфиге для подключения сервиса
 * @param string $service пути для подключения сервиса
 * @author Михаил Орехов
 */
class serviceloaderFactory
{
    public static function connect($groupСfg, $service)
    {
        //получить конфиг из реестра
        $cfg = registry::get("cfg");
        $service = $cfg['service'][$service];
        $src = $cfg['service'][$service]['src'];
        $class = $cfg['service'][$service]['class'];
        //проверка конфига
        if (isset($service)) {
            if (file_exists($src)) {
                require_once $src;
            } else {
                throw new Exception("file $src not found");
            }
            if (class_exists($class)) {
                return new $class($cfg[$groupСfg]);
            } else {
                throw new Exception("class $class not found");
            }
        } else {
            throw new Exception("service $service not exist");
        }
    }

}
