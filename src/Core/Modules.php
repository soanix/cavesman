<?php

namespace Cavesman;

use Cavesman\Tool\Parser\ClassName;

class Modules
{
    const string MODULE_DIR = _ROOT_ . "/src/Modules";
    /**
     * Autoload Modules
     * @return void
     */
    public static function autoload(): void
    {
        foreach (ClassName::listInNamespace('\\App\\Modules') as $module) {
            if (method_exists($module, 'autoload'))
                $module::autoload();

            if (method_exists($module, 'routes'))
                $module::routes();
        }
    }
}
