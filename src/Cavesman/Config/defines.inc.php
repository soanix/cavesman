<?php
//config dir of app

$dir = dirname(__FILE__);

// Find root directory
do {$dir = $dir."/..";}while(!is_dir($dir."/vendor"));

define("_ROOT_", $dir);

define("_APP_", _ROOT_."/app");

define("_SRC_", _ROOT_."/src");

define("DEFAULT_THEME", "public");

define("_PATH_", "/");

define("_DEV_MODE_", true);

//config dir of app
define("_CONFIG_", _ROOT_."/Config");
//Classes main dir
define("_CLASSES_", _SRC_."/Classes");
//Controllers main dir
define("_CONTROLLERS_", _SRC_."/Controllers");
//Themes main dir
define("_THEMES_", _SRC_."/Themes");

//Tools main dir
define("_TOOLS_", _ROOT_."/Tools");
//Modules main dir
define("_MODULES_", _SRC_."/Modules");
//Language main dir
define("_LANG_", _APP_."/Lang");
//Cache main dir
define("_CACHE_", _APP_."/Cache");


define("_THEME_NAME_", isset($_SESSION['theme']) && $_SESSION['theme'] ? $_SESSION['theme'] : DEFAULT_THEME);

define("_THEME_", _THEMES_."/"._THEME_NAME_);

/** RELATIVE PATHS**/

define("_DOMAIN_", isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "NONE");

// Browser path for media
define("_TEMPLATES_", _PATH_."Themes");


?>
