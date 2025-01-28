<?php

namespace Cavesman;

final class Display
{
   
    public static $instance;

    /**
     * Init function to load Smarty
     */
    public static function init(): void
    {
        if (is_dir(Fs::SRC_DIR . "/Routes"))
            foreach (glob(Fs::SRC_DIR . "/Routes/*.php") as $routeFile)
                require_once $routeFile;
        Modules::autoload();

    }
    public static function initCli(): void
    {

        if (is_dir(Fs::SRC_DIR . "/Commands"))
            foreach (glob(Fs::SRC_DIR . "/Commands/*.php") as $routeFile)
                require_once $routeFile;

        Modules::autoload();
    }
}
