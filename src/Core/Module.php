<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Cavesman\Tool\Parser\ClassName;

class Module
{
    /**
     * Autoload Modules
     * @return void
     */
    public static function autoload(): void
    {
        $modules = [];
        /**
         * AUTOLOAD PSR-4
         */
        foreach (glob(FileSystem::getPath(Directory::MODULE) . '/*') as $prefix => $path) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {

                if($file->isFile()) {
                    $tokens = token_get_all(file_get_contents($file->getRealPath()));

                    foreach ($tokens as $token) {
                        if (is_array($token) && ($token[0] === T_CLASS || $token[0] === T_INTERFACE || $token[0] === T_TRAIT)) {
                            require_once $file->getRealPath();
                        }
                    }
                }
            }
        }
        foreach (ClassName::listInNamespace('\\App\\Module') as $module) {
            if (method_exists($module, 'autoload'))
                $module::init();

            if (method_exists($module, 'routes'))
                $module::routes();

            if (method_exists($module, 'smarty'))
                $module::smarty();
        }
    }
}
