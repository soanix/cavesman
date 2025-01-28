<?php

namespace Cavesman;

use Cavesman\Tool\Parser\ClassName;

class Module
{
    /**
     * Autoload Modules
     * @return void
     */
    public static function autoload(): void
    {
        foreach (ClassName::listInNamespace('\\App\\Module') as $module) {
            if (method_exists($module, 'autoload'))
                $module::autoload();

            if (method_exists($module, 'routes'))
                $module::routes();
        }
    }
}
