<?php
namespace Cavesman;

class DB extends \PDO{
    function __construct(){ // Se conecta a mysqli
        if(!defined("DB_SERVER"))
            throw new \Exception("Define primero los DB_SERVER, DB_NAME, DB_USER y DB_PASSWORD en el archovo settings.inc.php", 1);
        parent::__construct('mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
    }
}
