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

    public static function get(string $config = '', $default = NULL){
        $params = explode(".", $config);
        $file = _APP_."/config/" . $params[0]  .".json";
        $config = [];
        if(file_exists($file)){
            $config = json_decode(file_get_contents($file), true);
        }

        // Env config
        $file = _APP_."/config/" . $params[0]  .".". self::getEnv() .".json";
        if(file_exists($file)){
            $config = array_replace_recursive($config, json_decode(file_get_contents($file), true));
        }
        if(file_exists($file)){
            foreach($params as $key =>  $param){
                if($key){
                    if(isset($config[$param]))
                        $config = $config[$param];
                    else
                        return $default;
                }
            }
        }

        return $config;
    }
}
