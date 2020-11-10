<?php

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}


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

define("_DATA_", _ROOT_."/data");

define("_SRC_", _ROOT_."/src");

define("_WEB_", _ROOT_."/web");
if(is_array(Cavesman\Config::get('params.theme')))
    if(isset($_SESSION['show_admin']) && $_SESSION['show_admin'])
        define("DEFAULT_THEME", Cavesman\Config::get('params.theme.admin'));
    else
        define("DEFAULT_THEME", Cavesman\Config::get('params.theme.public'));
else
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

/** RELATIVE PATHS**/
if (PHP_SAPI !== 'cli'){
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https';
    }else{
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
    }
    define("_DOMAIN_", $protocol . "://" . $_SERVER['HTTP_HOST']);
}else{
    define("_DOMAIN_", "localhost");
}

// Browser path for media
define("_TEMPLATES_", _PATH_."themes");


?>
