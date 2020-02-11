<?php

namespace Cavesman;

class Config {
    public static function getEnv(){
        $file = _APP_."/config/main.json";
        if(file_exists($file)){
            $main = json_decode(file_get_contents($file), true);
        }
        if(isset($main['env']))
            return $main['env'];
        return 'dev';
    }
    public static function get($config = NULL) {
        $file = _APP_."/config/" . $config  .".". self::getEnv() .".json";
        if(file_exists($file)){
            return json_decode(file_get_contents($file), true);
        }

        return null;
    }

}
