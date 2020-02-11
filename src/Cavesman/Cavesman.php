<?php
namespace Cavesman;

class Cavesman {

    public static $env = 'dev';
    public static $router;
    public static $smarty;

    public static function __install(){
        self::$router = self::getInstance(Router::class);
        self::$smarty = self::getInstance(Smarty::class);
    }

    public static function run($env = 'dev') {
        self::$env = $env;
        Display::startTheme();
        Display::theme();
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
