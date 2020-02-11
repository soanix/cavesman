<?php
namespace Cavesman;

class Cavesman {
    public static $router;
    public static $smarty;

    public static function __install(){
        self::$router = self::getInstance(Router::class);
        self::$smarty = self::getInstance(Smarty::class);
    }

    public static function run(string $env = 'dev') : void
    {
        if (PHP_SAPI !== 'cli'){
            Display::startTheme();
            Display::theme();
        }
    }
    public static function getInstance($module) : object
    {
        if(($module::$instance instanceof $module) === false)
        {
            $module::$instance = new $module();
        }
        return $module::$instance;
    }
}
?>
