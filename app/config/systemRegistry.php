<?php

/**
 * простой реестр для доступа к обьектам
 * тот же сингелтон только но он возвращает не единственный экземпляр самого себя а обьекты которые были в него помещены
 */
class systemRegistry
{

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    private static $instance = array();

    /**
     * Добавить обьект в реестр 
     * @param obj|arr $obj добавляемый обьект(ы)
     * @param string $name если требуется свое имя для обьекта
     * @param bool $reload если требуется перезаписать обьект
     */
    public static function set($obj, $name = '', $reload = '')
    {
        //добавить массив обьектов в реестр
        if (is_array($obj)) {
            foreach($obj as $instance){
                self::set($instance);
            }
            return;
        }
        //обычное добавление
        if (empty($name)) {
            if (!$name = get_class($obj))
                throw new Exception("\n object $obj is not exist \n");
        } else {
            if (!is_string($name))
                throw new Exception("\n name variable fail \n");
        }
        if (empty(self::$instance[$name]) || !empty($reload)) {
            self::$instance[$name] = $obj;
        }
    }

    /**
     * Получить обькт из реестра
     * @param string $name
     * @return instance
     * @throws Exception
     */
    public static function get($name)
    {
        if (isset(self::$instance[$name])) {
            return self::$instance[$name];
        } else {
            //return FALSE;
            throw new Exception("\n object $name does not exist in the registry \n");
        }
    }

}
