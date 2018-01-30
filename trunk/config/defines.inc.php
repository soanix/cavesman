<?

define("_DEV_MODE_", true);

/** DEFAULTS **/

define("DEFAULT_LANG", "es");

/* IS LOGGED */
define("isLogged", (isset($_SESSION['user']) && $_SESSION['user'] ? true : false));

/** ABSOLUTE PATHS**/


//ROOT dir of app
define("_ROOT_", dirname(__FILE__)."/..");
//config dir of app
define("_CONFIG_", _ROOT_."/config");
//Classes main dir
define("_CLASSES_", _ROOT_."/classes");
//Controllers main dir
define("_CONTROLLERS_", _ROOT_."/controllers");
//Themes main dir
define("_THEMES_", _ROOT_."/themes");
//Tools main dir
define("_TOOLS_", _ROOT_."/tools");
//Modules main dir
define("_MODULES_", _ROOT_."/modules");
//Language main dir
define("_LANG_", _ROOT_."/lang");
//Cache main dir
define("_CACHE_", _ROOT_."/cache");

require_once(_CONFIG_."/setup.inc.php");

$permisos = array(
	"admin" => 1,
	"public" => ""
);
$theme = isset($_GET['theme']) ? $_GET['theme'] : (isset($_SESSION['theme']) ? $_SESSION['theme'] : DEFAULT_THEME);
include_once(_CLASSES_."/db.class.php");
define("_THEME_NAME_", $theme);


/** RELATIVE PATHS**/

define("_DOMAIN_", $_SERVER['SERVER_NAME']);

// Browser path for media
define("_PATH_", "/");
define("_TEMPLATES_", _PATH_."themes");


?>
