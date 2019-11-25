<?php
namespace Cavesman;

class Cavesman {

    public static $env = 'dev';
    public static $router;
    public static $smarty;

    function __construct(){
        self::$router = self::getInstance(Router::class);
        self::$smarty = self::getInstance(Smarty::class);
    }

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
