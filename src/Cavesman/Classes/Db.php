<?php

namespace Cavesman;

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

        $directories = scandir(_MODULES_);
        $paths = [];
        if (is_dir(_ROOT_ . "/src/Entity"))
            $paths[] = _ROOT_ . "/src/Entity";
        if (Config::get('params.db.global_aux', false)) {
            if (is_dir(_ROOT_ . "/src/EntityAux"))
                $paths[] = _ROOT_ . "/src/EntityAux";
        }
        if (is_dir(_MODULES_)) {
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {

                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    if ($config['active']) {

                        if (is_dir(_MODULES_ . "/" . $directory . "/Abstract"))
                            foreach (glob(_MODULES_ . "/" . $directory . "/Abstract/*.php") as $filename)
                                require_once $filename;

                        if (is_dir(_MODULES_ . "/" . $directory . "/Entity"))
                            $paths[] = _MODULES_ . "/" . $directory . "/Entity";

                        if (Config::get('params.db.global_aux', false))
                            if (is_dir(_MODULES_ . "/" . $directory . "/EntityAux"))
                                $paths[] = _MODULES_ . "/" . $directory . "/EntityAux";

                    }
                }
            }
        }
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: true,
            cache: null
        );

        $connectionParams = Config::get($file . '.' . $server);

        if ($database)
            $connectionParams['dbname'] = $database;

        $connection = DriverManager::getConnection($connectionParams);

        self::$oConnection[$key] = new EntityManager($connection, $config);

        return self::$oConnection[$key];
    }
}
