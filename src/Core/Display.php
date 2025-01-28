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
        if (is_dir(FileSystem::srcDir() . "/Routes"))
            foreach (glob(FileSystem::srcDir() . "/Routes/*.php") as $routeFile)
                require_once $routeFile;
        Modules::autoload();

    }

    public static function initCli(): void
    {

        if (is_dir(FileSystem::srcDir() . "/Commands"))
            foreach (glob(FileSystem::srcDir() . "/Commands/*.php") as $routeFile)
                require_once $routeFile;

        Modules::autoload();
    }
}
