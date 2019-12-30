<?php
namespace Cavesman;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DB extends \PDO
{
    protected static $oConnection = false;

    public static function getManager() {
        if(self::$oConnection instanceof EntityManager !== false){
            return self::$oConnection;
        }
        if (
            !isset(Config::get("db")['host'])
            || !isset(Config::get("db")['user'])
            || !isset(Config::get("db")['password'])
            || !isset(Config::get("db")['dbname'])
        ){
            throw new \Exception("El archivo config/db.json no existe o no esta correctamente configurado", 1);
        }

        $directories = scandir(_MODULES_);
        $directoryEntity = array();
        if(is_dir(_MODULES_)){
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    if ($config['active']) {
                        if(is_dir(_MODULES_ . "/" . $directory . "/entity"))
                            array_push($directoryEntity, _MODULES_ . "/" . $directory . "/entity");
                    }
                }
            }
        }
        $config = Setup::createAnnotationMetadataConfiguration($directoryEntity, true, null, null, false);
        $connectionParams = Config::get("db");
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        self::$oConnection =  EntityManager::create($conn, $config);

        return self::$oConnection;
    }
}
