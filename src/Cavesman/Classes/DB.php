<?php
namespace Cavesman;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DB extends \PDO
{

    function __construct() // Se conecta a mysqli
    {
        if (!defined("DB_SERVER"))
            throw new \Exception("Define primero los DB_SERVER, DB_NAME, DB_USER y DB_PASSWORD en el archovo settings.inc.php", 1);
    }
    protected static $oConnection;

    public static function getInstance()
    {
        if ((self::$oConnection instanceof parent) === false) {
            self::$oConnection = new parent('mysql:host=' . \Cavesman\Config::get("db")['host'] . ';dbname=' . \Cavesman\Config::get("db")['database'] . ';charset=utf8', \Cavesman\Config::get("db")['user'], \Cavesman\Config::get("db")['password']);
        }
        return self::$oConnection;
    }
    public static function getManager() {
        $directories = scandir(_MODULES_);
        $directoryEntity = array();
        foreach ($directories as $directory) {
            $module = str_replace('directory/', '', $directory);
            if ($module !== '.' && $module != '..') {
                $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                if ($config['active']) {
                    if(is_dir(_MODULES_ . "/" . $directory . "/Entity"))
                        array_push($directoryEntity, _MODULES_ . "/" . $directory . "/Entity");
                }
            }
        }
        $config = Setup::createAnnotationMetadataConfiguration($directoryEntity, true, null, null, false);
        $connectionParams = array(
            'dbname' => \Cavesman\Config::get("db")['database'],
            'user' => \Cavesman\Config::get("db")['user'],
            'password' => \Cavesman\Config::get("db")['password'],
            'host' => \Cavesman\Config::get("db")['host'],
            'driver' => 'pdo_mysql',
            'charset' => "utf8mb4"
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        return EntityManager::create($conn, $config);
    }
}
