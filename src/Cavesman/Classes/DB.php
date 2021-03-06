<?php
namespace Cavesman;

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;
class DB extends \PDO
{
    protected static $oConnection = false;

    public static function getManager($db = 'local') {
        if(self::$oConnection instanceof EntityManager !== false){
            return self::$oConnection;
        }

        $directories = scandir(_MODULES_);
        $directoryEntity = [];
        if(is_dir(_ROOT_."/src/entity"))
            $directoryEntity[] = _ROOT_."/src/entity";
        if(is_dir(_MODULES_)){
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {

                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    if ($config['active']) {

                        if(is_dir(_MODULES_ . "/" . $directory . "/abstract")){
                            foreach (glob(_MODULES_ . "/" . $directory . "/abstract/*.php") as $filename) {
                                require_once $filename;
                            }
                        }

                        if(is_dir(_MODULES_ . "/" . $directory . "/entity"))
                            array_push($directoryEntity, _MODULES_ . "/" . $directory . "/entity");
                    }
                }
            }
        }
        $config = Setup::createAnnotationMetadataConfiguration($directoryEntity, true, null, null, false);
        $connectionParams = Config::get("db.".$db);


        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        self::$oConnection =  EntityManager::create($conn, $config);

        return self::$oConnection;
    }
    public static function getCli(){

        /* @var $entityManager \Doctrine\ORM\EntityManagerInterface */
        $entityManager = self::getManager("cli");
        $connectionHelper = new ConnectionHelper($entityManager->getConnection());
        $helperset = new HelperSet([
            'em'         => new EntityManagerHelper($entityManager),
            'db'         => $connectionHelper,
            'connection' => $connectionHelper,
        ]);
        ConsoleRunner::run($helperset);
    }
}
