<?php

use Cavesman\Config;


//config dir of app
if (!defined("_ROOT_") && is_dir($_SERVER['DOCUMENT_ROOT'] . "/../vendor"))
    define("_ROOT_", $_SERVER['DOCUMENT_ROOT'] . "/..");
elseif (!defined("_ROOT_") && is_dir($_SERVER['DOCUMENT_ROOT'] . "/vendor"))
    define("_ROOT_", $_SERVER['DOCUMENT_ROOT']);
elseif (!defined("_ROOT_") && isset($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/../vendor"))
    define("_ROOT_", $_SERVER['PWD'] . "/..");
elseif (!defined("_ROOT_") && isset($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/vendor"))
    define("_ROOT_", $_SERVER['PWD']);
elseif (!defined("_ROOT_") && is_dir(getcwd() . "/../vendor"))
    define("_ROOT_", getcwd() . "/..");
elseif (!defined("_ROOT_") && is_dir(getcwd() . "/vendor"))
    define("_ROOT_", getcwd());

define("_APP_", _ROOT_ . "/app");

define("_SRC_", _ROOT_ . "/src");

define("_WEB_", _ROOT_ . "/web");

if (Config::get('params.session.enabled', true) && PHP_SAPI !== 'cli') {
    session_start();
}

if (is_array(Cavesman\Config::get('params.theme', 'public')))
    if (isset($_SESSION['show_admin']) && $_SESSION['show_admin'])
        define("DEFAULT_THEME", Cavesman\Config::get('params.theme.admin', 'admin'));
    else
        define("DEFAULT_THEME", Cavesman\Config::get('params.theme.public', 'public'));
else
    define("DEFAULT_THEME", Cavesman\Config::get('params.theme', 'public'));

define("_PATH_", "/");

define("_DEV_MODE_", Config::get("params.debug"));

//config dir of app
define("_CONFIG_", _ROOT_ . "/Config");
//Classes main dir
define("_CLASSES_", _SRC_ . "/Classes");
//Controllers main dir
define("_CONTROLLERS_", _SRC_ . "/Controllers");
//Themes main dir
define("_THEMES_", _SRC_ . "/Views");

//Modules main dir
define("_MODULES_", _SRC_ . "/Modules");
//Language main dir
define("_LANG_", _APP_ . "/lang");
//Cache main dir
define("_CACHE_", _APP_ . "/cache");

//define("_THEME_NAME_", isset($_SESSION['theme']) && $_SESSION['theme'] ? $_SESSION['theme'] : DEFAULT_THEME);
define("_THEME_NAME_", DEFAULT_THEME);


define("_THEME_", _THEMES_ . "/" . _THEME_NAME_);
/** RELATIVE PATHS**/

/** RELATIVE PATHS**/
if (PHP_SAPI !== 'cli') {
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https';
    } else {
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
    }
    define("_DOMAIN_", $protocol . "://" . $_SERVER['HTTP_HOST']);
} else {
    define("_DOMAIN_", "localhost");
}

// Browser path for media
define("_TEMPLATES_", _PATH_ . "Views");


?>
