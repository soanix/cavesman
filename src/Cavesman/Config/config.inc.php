<?php

session_start();

date_default_timezone_set('Europe/Madrid');

if(Cavesman\Config::get('params.debug', true)){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL ^ E_DEPRECATED);
}else{
    ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}
?>
