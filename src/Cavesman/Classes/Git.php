<?php

namespace Cavesman;

use DateTime;
use DateTimeZone;

class Git
{
    public static function version($params, $smarty)
    {
       return substr(file_get_contents(dirname(__FILE__).'/../../../.git/refs/heads/master'),0,7);
    }
}
