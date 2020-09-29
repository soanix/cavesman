<?php
//config dir of app
if(!defined("_ROOT_") && is_dir($_SERVER['DOCUMENT_ROOT']."/../vendor"))
    define("_ROOT_", $_SERVER['DOCUMENT_ROOT']."/..");
elseif(!defined("_ROOT_") && is_dir($_SERVER['DOCUMENT_ROOT']."/vendor"))
    define("_ROOT_", $_SERVER['DOCUMENT_ROOT']);
elseif(!defined("_ROOT_") && isset($_SERVER['PWD']) && is_dir($_SERVER['PWD']."/../vendor"))
    define("_ROOT_", $_SERVER['PWD']."/..");
elseif(!defined("_ROOT_") && isset($_SERVER['PWD']) &&  is_dir($_SERVER['PWD']."/vendor"))
    define("_ROOT_", $_SERVER['PWD']);
elseif(!defined("_ROOT_") && is_dir(getcwd()."/../vendor"))
    define("_ROOT_", getcwd()."/..");
elseif(!defined("_ROOT_") && is_dir(getcwd()."/vendor"))
    define("_ROOT_", getcwd());

define("_APP_", _ROOT_."/app");

define("_SRC_", _ROOT_."/src");

define("_WEB_", _ROOT_."/web");

define("DEFAULT_THEME", Cavesman\Config::get('params.theme'));

define("_PATH_", "/");

define("_DEV_MODE_", true);

//config dir of app
define("_CONFIG_", _ROOT_."/Config");
//Classes main dir
define("_CLASSES_", _SRC_."/classes");
//Controllers main dir
define("_CONTROLLERS_", _SRC_."/controllers");
//Themes main dir
define("_THEMES_", _SRC_."/themes");

//Tools main dir
define("_TOOLS_", _ROOT_."/tools");
//Modules main dir
define("_MODULES_", _SRC_."/modules");
//Language main dir
define("_LANG_", _APP_."/lang");
//Cache main dir
define("_CACHE_", _APP_."/cache");

//define("_THEME_NAME_", isset($_SESSION['theme']) && $_SESSION['theme'] ? $_SESSION['theme'] : DEFAULT_THEME);
define("_THEME_NAME_", DEFAULT_THEME);

define("_THEME_", _THEMES_."/"._THEME_NAME_);

/** RELATIVE PATHS**/

if(strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,4))=='http') {
   $strOut = sprintf('http://%s:%d',
                  $_SERVER['SERVER_NAME'],
                  $_SERVER['SERVER_PORT']);
} else {
    $strOut = sprintf('https://%s:%d',
                  $_SERVER['SERVER_NAME'],
                  $_SERVER['SERVER_PORT']);
}


define("_DOMAIN_", $strOut);

// Browser path for media
define("_TEMPLATES_", _PATH_."themes");


?>
