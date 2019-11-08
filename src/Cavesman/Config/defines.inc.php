<?php
//config dir of app
$projectRootPath = dirname(\Composer\Factory::getComposerFile());

define("_ROOT_", $projectRootPath);

define("_APP_", _ROOT_."/src");

//config dir of app
define("_CONFIG_", _ROOT_."/Config");
//Classes main dir
define("_CLASSES_", _ROOT_."/Classes");
//Controllers main dir
define("_CONTROLLERS_", _ROOT_."/Controllers");
//Themes main dir
define("_THEMES_", _APP_."/Themes");

//Tools main dir
define("_TOOLS_", _ROOT_."/Tools");
//Modules main dir
define("_MODULES_", _APP_."/Modules");
//Language main dir
define("_LANG_", _APP_."/Lang");
//Cache main dir
define("_CACHE_", _APP_."/Cache");


require_once(_CONFIG_."/setup.inc.php");


define("_THEME_NAME_", isset($_SESSION['theme']) && $_SESSION['theme'] ? $_SESSION['theme'] : DEFAULT_THEME);

define("_THEME_", _THEMES_."/"._THEME_NAME_);

/** RELATIVE PATHS**/

define("_DOMAIN_", isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "NONE");

// Browser path for media
define("_TEMPLATES_", _PATH_."Themes");


?>
