<?php

namespace Cavesman;

class Launcher
{

    public static function getInstance($module): object
    {
        if (($module::$instance instanceof $module) === false) {
            $module::$instance = new $module();
        }
        return $module::$instance;
    }

    public static function run(string $env = 'dev'): void
    {


        define('_ROOT_', Fs::getRootDir());

        date_default_timezone_set(Config::get('params.timezone', 'Europe/Madrid'));

        if (Config::get('params.debug', true)) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);
        }

        /**
         * Sometimes we not need session starts when use JWT or other auth method
         * that not requires PHP Sessions or when we use PHP CLI
         */


        if (PHP_SAPI !== 'cli') {
            if (Config::get('params.session.enabled', true))
                session_start();

            Display::init();
            Router::run();
        } else {
            Display::initCli();
            Console::run();
        }
    }

    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }
}