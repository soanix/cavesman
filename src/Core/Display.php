<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use FilesystemIterator;

class Display
{

    /**
     * Init function to load Smarty
     */
    public static function init(): void
    {
        Module::autoload();

        self::requirePhpFilesRecursively(FileSystem::getPath(Directory::ROUTES));
    }

    public static function initCli(): void
    {
        Module::autoload();

        self::requirePhpFilesRecursively(FileSystem::getPath(Directory::ROUTES));
        self::requirePhpFilesRecursively(FileSystem::getPath(Directory::COMMANDS));
    }

    private static function requirePhpFilesRecursively(string $dir): void
    {
        if (!is_dir($dir))
            return;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php')
                require_once $file->getPathname();
        }
    }
}
