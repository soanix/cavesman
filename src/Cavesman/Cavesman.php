<?php
namespace Cavesman;

class Cavesman
{
    public static function __install()
    {

    }

    public static function getInstance($module): object
    {
        if (($module::$instance instanceof $module) === false) {
            $module::$instance = new $module();
        }
        return $module::$instance;
    }

    public static function run(string $env = 'dev'): void
    {
        if (PHP_SAPI !== 'cli') {
            Display::startTheme();
            Display::theme();
            Router::run();
        }else {
            Display::startCli();
            Console::run();
        }
    }
}

?>
