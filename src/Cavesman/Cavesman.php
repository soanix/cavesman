<?php

namespace Cavesman;

class Cavesman
{
    public static function __install()
    {

    }

    public static function getInstance($module): object
    {
        if (($module::$instance instanceof $module) === false) {
            $module::$instance = new $module();
        }
        return $module::$instance;
    }

    public static function run(string $env = 'dev'): void
    {
        if (PHP_SAPI !== 'cli') {
            Display::startTheme();
            Display::theme();
            Router::run();
        }else {
            Display::startCli();
            Console::run();
        }
    }
}

Console::command('cavesman:install', function () {
    include __DIR__ . '/Command/Install.php';
}, 'Install filesystem (Only first run)');

Console::command('--help', Console::class . '@listRoutesCommand', 'Show all commands');
Console::command('route:list', Router::class . '@listRoutesCommand', 'List All routes with methods');

/** @see Router::listRoutesCommand() */
Console::command('route:list:(simple|complete)', Router::class . '@listRoutesCommand', 'List all url patterns with or wothout methods');
