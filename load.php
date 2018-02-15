<?php

require_once "config/config.inc.php";
if(file_exists(_ROOT_.'/vendor/autoload.php'))
    require_once _ROOT_.'/vendor/autoload.php';
else
    throw new \Exception("Es necesario usar composer", 1);

require_once _ROOT_."/controller.php";
?>
