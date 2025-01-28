<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Db
{
    /** @var array $oConnection */
    protected static array $oConnection = [];

    public static function getCli(): void
    {

        ConsoleRunner::run(
            new SingleManagerProvider(self::getManager('cli'))
        );
    }

    public static function getManager($server = 'local', ?string $database = null, ?string $file = 'db')
    {
        $key = $server . ':' . $database;

        if (isset(self::$oConnection[$key]) && self::$oConnection[$key] instanceof EntityManager) {
            return self::$oConnection[$key];
        }


        $paths = [];
        if (is_dir(FileSystem::getPath(Directory::ENTITY)))
            $paths[] = FileSystem::getPath(Directory::ENTITY);
        if (Config::get('params.db.global_aux', false)) {
            if (is_dir(FileSystem::getPath(Directory::SRC) . '/EntityAux'))
                $paths[] = FileSystem::getPath(Directory::SRC) . '/EntityAux';
        }
        if (is_dir(FileSystem::getPath(Directory::MODULE))) {
            $directories = scandir(FileSystem::getPath(Directory::MODULE));
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {

                    $config = json_decode(file_get_contents(FileSystem::getPath(Directory::MODULE) . "/" . $directory . "/config.json"), true);
                    if ($config['active']) {

                        if (is_dir(FileSystem::getPath(Directory::MODULE) . "/" . $directory . "/Entity"))
                            $paths[] = FileSystem::getPath(Directory::MODULE) . "/" . $directory . "/Entity";

                        if (Config::get('params.db.global_aux', false))
                            if (is_dir(FileSystem::getPath(Directory::MODULE) . "/" . $directory . "/EntityAux"))
                                $paths[] =FileSystem::getPath(Directory::MODULE) . "/" . $directory . "/EntityAux";

                    }
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
