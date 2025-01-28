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
        if (is_dir(FileSystem::getPath(Directory::ROUTES)))
            foreach (glob(FileSystem::getPath(Directory::ROUTES) . "/*.php") as $routeFile)
                require_once $routeFile;
        Module::autoload();

    }

    public static function initCli(): void
    {
        if (is_dir(FileSystem::getPath(Directory::ROUTES)))
            foreach (glob(FileSystem::getPath(Directory::ROUTES) . "/*.php") as $routeFile)
                require_once $routeFile;
        
        if (is_dir(FileSystem::getPath(Directory::COMMANDS)))
            foreach (glob(FileSystem::getPath(Directory::COMMANDS) . "/*.php") as $routeFile)
                require_once $routeFile;

        Module::autoload();
    }
}
