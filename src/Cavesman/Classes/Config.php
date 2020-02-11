<?php

namespace Cavesman;

class Config {

    public static function get($config = NULL) {
        $file = _APP_."/config/" . $config  .".". Cavesman::$env .".json";
        if(file_exists($file)){
            return json_decode(file_get_contents($file), true);
        }
        $file = _APP_."/config/" . $config  .".json";
        if(file_exists($file)){
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }

}
