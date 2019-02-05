<?php

define("_DEV_MODE_", true);

//config dir of app
define("_CONFIG_", _ROOT_."/config");
//Classes main dir
define("_CLASSES_", _ROOT_."/classes");
//Controllers main dir
define("_CONTROLLERS_", _ROOT_."/controllers");
//Themes main dir
define("_THEMES_", _APP_."/themes");
//Tools main dir
define("_TOOLS_", _ROOT_."/tools");
//Modules main dir
define("_MODULES_", _APP_."/modules");
//Language main dir
define("_LANG_", _APP_."/lang");
//Cache main dir
define("_CACHE_", _APP_."/cache");


require_once(_CONFIG_."/setup.inc.php");


define("_THEME_NAME_", isset($_SESSION['theme']) && $_SESSION['theme'] ? $_SESSION['theme'] : DEFAULT_THEME);


/** RELATIVE PATHS**/

define("_DOMAIN_", isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "NONE");

// Browser path for media
define("_TEMPLATES_", _PATH_."themes");


?>
