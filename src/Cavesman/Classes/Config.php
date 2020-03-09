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
    public static function get($config = '') {
        $params = explode(".", $config);

        $file = _APP_."/config/" . $params[0]  .".". self::getEnv() .".json";
        $array = [];
        if(file_exists($file)){
            $array =  json_decode(file_get_contents($file), true);
            foreach($params as $key =>  $param){
                if($key){
                    $array = $array[$param] ?? [];
                    if(!$array)
                        return self::getDefault($params);
                }
            }
        }
        return $array;
    }
    private static function getDefault($params = []) {
        $file = _APP_."/config/" . $params[0]  .".json";
        $array = [];
        if(file_exists($file)){
            $array =  json_decode(file_get_contents($file), true);
            foreach($params as $key => $param){
                if($key){
                    $array = $array[$param] ?? [];
                    if(!$array)
                        return [];
                }
            }
        }
        return $array;
    }
}
