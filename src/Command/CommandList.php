<?php

use Cavesman\Console;
use Cavesman\Router;

Console::command('install', function () {
    include __DIR__ . '/Install.php';
}, 'Install filesystem (Only first run)');

Console::command('doctrine:entity', function () {
    include __DIR__ . '/Doctrine.php';
}, 'Create entity step by step');

Console::command('--help', Console::class . '@listRoutesCommand', 'Show all commands');
Console::command('route:list', Router::class . '@listRoutesCommand', 'List All routes with methods');

/** @see Router::listRoutesCommand() */
Console::command('route:list:(simple|complete)', Router::class . '@listRoutesCommand', 'List all url patterns with or wothout methods');