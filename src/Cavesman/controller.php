<?php
if($access){
    $display->theme = _THEME_NAME_;
}else
    $display->theme = "mantenimiento";

$display->startTheme();
$display->theme();
?>
