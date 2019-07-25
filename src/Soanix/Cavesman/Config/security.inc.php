<?php

$code = isset($_GET['code']) ? $_GET['code'] : '';
if($code != date("dmY"))
    $access = false;
else
    $access = true;
