<?php

namespace Cavesman;

class Git
{
    public static function version($params, $smarty)
    {
        $tag = trim(shell_exec("cd " . dirname(__FILE__) . " && git describe --tags"));
        $short = trim(shell_exec("cd " . dirname(__FILE__) . " && git rev-parse --short HEAD"));

        return $tag ?: $short;
    }
}
