<?
session_start();

date_default_timezone_set('Europe/Madrid');

require_once("defines.inc.php");
if(_DEV_MODE_){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL | E_WARNING);
}
require_once(_CONFIG_."/includes.inc.php");
$display = new Display();
?>
