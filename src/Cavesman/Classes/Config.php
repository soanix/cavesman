<?php

namespace Cavesman;

class Config {

    public static function get($config = NULL) {
        if(file_exists(_APP_."/config/" . $config  . ".json")){
            return json_decode($config, false);
        }
        return null;
    }

}
