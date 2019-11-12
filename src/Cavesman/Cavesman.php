<?php

class Cavesman {

    public static $env = 'dev';

    public static function run($env = 'dev') {
        self::$env = $env;
        echo "RUNS ". self::$env;
    }
}
?>
