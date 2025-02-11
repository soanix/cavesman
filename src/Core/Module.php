<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Cavesman\Exception\ModuleException;
use Cavesman\Tool\Parser\ClassName;

class Module
{
    /** @var Model\Module[] $list */
    private static array $list = [];

    /**
     * Autoload Modules
     * @return void
     * @throws ModuleException
     */
    public static function autoload(): void
    {
        $modules = [];
        /**
         * AUTOLOAD PSR-4
         */
        foreach (self::list() as $module) {

            if(!$module->active)
                continue;

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($module->path)) as $file) {

                if ($file->isFile()) {
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
                $module::autoload();

            if (method_exists($module, 'routes'))
                $module::routes();

            if (method_exists($module, 'smarty'))
                $module::smarty();

            if (method_exists($module, 'twig'))
                $module::twig();
        }
    }

    /**
     * Retrieve list of modules
     *
     * @return Model\Module[]
     * @throws ModuleException
     */
    public static function list(): array
    {

        if (self::$list)
            return self::$list;

        foreach (glob(FileSystem::getPath(Directory::MODULE) . '/*', GLOB_ONLYDIR) as $path) {


            $config = json_decode(file_get_contents($path . '/config.json'), true);

            if (!$config)
                throw new ModuleException('Module config file is empty or corrupt');

            $currentConfig = Config::get('modules.' . $config['name'], $config);

            $module = new Model\Module($currentConfig);
            $module->path = $path;

            self::$list[] = $module;

        }
        return self::$list;
    }
}
