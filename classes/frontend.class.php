<?php

namespace Cavesman;

use \Cavesman\Router;
use \Cavesman\SmartyCustom;

class FrontEnd {
    public $router;
    public $smarty;
    function __construct(){
        $this->router = self::getInstance("Cavesman\Router");
        $this->smarty = self::getInstance("Cavesman\Smarty");
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
