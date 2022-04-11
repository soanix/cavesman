<?php

namespace Cavesman;

use DateTime;
use DateTimeZone;

class Git
{
    public static function version($params, $smarty)
    {
        $HEAD_hash = file_get_contents(dirname(__FILE__) . '/../../../.git/refs/heads/master'); // or branch x

        $files = glob(dirname(__FILE__) . '/../../../.git/refs/tags/*');
        foreach(array_reverse($files) as $file) {
            $contents = trim(file_get_contents($file));

            if($HEAD_hash === $contents)
            {
                exit('Current tag is ' . basename($file));
            }
        }

        exit('No matching tag');
    }
}
