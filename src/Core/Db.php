<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Cavesman\Exception\ModuleException;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Db
{
    /** @var array $oConnection */
    protected static array $oConnection = [];

    public static function getCli($server = 'cli', ?string $database = null, ?string $file = 'db'): void
    {

        ConsoleRunner::run(
            new SingleManagerProvider(self::getManager($server, $database, $file))
        );
    }

    /**
     * @throws ModuleException
     */
    public static function getManager($server = 'local', ?string $database = null, ?string $file = 'db')
    {
        $key = $server . ':' . $database;

        if (isset(self::$oConnection[$key]) && self::$oConnection[$key] instanceof EntityManager) {
            return self::$oConnection[$key];
        }

        $directories = Config::get($file . '.' . $server . '.entities', ['Entity']);

        $paths = [];

        foreach ($directories as $directory)
            if (file_exists(FileSystem::getPath(Directory::SRC) . '/' . $directory))
                $paths[] = FileSystem::getPath(Directory::SRC) . '/' . $directory;

        foreach (glob(FileSystem::getPath(Directory::MODULE) . '/*', GLOB_ONLYDIR) as $path) {


            $config = json_decode(file_get_contents($path . '/config.json'), true);

            if(!$config)
                throw new ModuleException('Module config file is empty or corrupt');

            $currentConfig = Config::get('modules.' . $config['name'], $config);

            if(!$currentConfig['active'])
                continue;

        }

        if (is_dir(FileSystem::getPath(Directory::MODULE))) {
            $directories = scandir(FileSystem::getPath(Directory::MODULE));
            foreach ($directories as $moduleDir) {
                $module = str_replace('directory/', '', $moduleDir);
                if ($module !== '.' && $module != '..') {
                    foreach ($directories as $directory)
                        if (file_exists(FileSystem::getPath(Directory::MODULE) . "/" . $moduleDir . "/" . $directory))
                            $paths[] = FileSystem::getPath(Directory::MODULE) . "/" . $moduleDir . "/" . $directory;
                }
            }
        }

        $connectionParams = Config::get($file . '.' . $server);

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: Config::get($file . '.' . $server . '.dev_mode', true)
        );


        if ($database)
            $connectionParams['dbname'] = $database;

        $connection = DriverManager::getConnection($connectionParams);

        self::$oConnection[$key] = new EntityManager($connection, $config);

        return self::$oConnection[$key];
    }
}
