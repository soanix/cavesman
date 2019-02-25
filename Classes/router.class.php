<?php
namespace Cavesman;

use Bramus\Router\Router as Bramus;

class Router extends Bramus{

    public static $instance;

    function __construct(){ // Se conecta a mysqli

    }


    public static function Build()
    {
        if((self::$instance instanceof Bramus) === false)
        {
            self::$instance = new Bramus();
        }
        return self::$instance;
    }
}

?>
