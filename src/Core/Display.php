<?php

namespace Cavesman;

use Cavesman\Enum\Directory;

class Display
{

    /**
     * Init function to load Smarty
     */
    public static function init(): void
    {
        Module::autoload();

        if (is_dir(FileSystem::getPath(Directory::ROUTES)))
            foreach (glob(FileSystem::getPath(Directory::ROUTES) . "/*.php") as $routeFile)
                require_once $routeFile;


    }

    public static function initCli(): void
    {
        Module::autoload();

        if (is_dir(FileSystem::getPath(Directory::ROUTES)))
            foreach (glob(FileSystem::getPath(Directory::ROUTES) . "/*.php") as $routeFile)
                require_once $routeFile;

        if (is_dir(FileSystem::getPath(Directory::COMMANDS)))
            foreach (glob(FileSystem::getPath(Directory::COMMANDS) . "/*.php") as $routeFile)
                require_once $routeFile;


    }
}
