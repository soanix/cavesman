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
    public static function get($config = NULL, $params = []) {
        $file = _APP_."/config/" . $config  .".". self::getEnv() .".json";
        $array = [];
        if(file_exists($file)){
            $array =  json_decode(file_get_contents($file), true);
            foreach($params as $param){
                $array = $array[$param] ?? [];
            }
        }

        return $array;
    }

}
