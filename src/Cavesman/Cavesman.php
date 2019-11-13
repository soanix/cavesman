<?php

class Cavesman {

    public static $env = 'dev';

    public static function run($env = 'dev') {
        self::$env = $env;
    }
    public static function getInstance($module)
    {
        if(($module::$instance instanceof $module) === false)
        {
            $module::$instance = new $module();
        }
        return $module::$instance;
    }
}
?>
