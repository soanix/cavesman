<?

if(file_exists(_ROOT_.'/vendor/autoload.php'))
    require_once _ROOT_.'/vendor/autoload.php';
else
    exit("Instala composer antes de usar Cavesman");

//add security file
include_once(_CONFIG_."/security.inc.php");

//add smarty custom class
include_once(_CLASSES_."/smarty.class.php");

// add modules class
include_once(_CLASSES_."/modules.class.php");

//add Disply class
include_once(_CLASSES_."/display.class.php");
