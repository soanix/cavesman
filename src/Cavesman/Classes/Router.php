<?php
namespace Cavesman;


class Router extends \Soanix\Router\Router
{
    public static function colorizeMethod($method, $string)
    {
        $colors = [
            'GET' => "\033[1;32m",       // Negrita + Verde
            'POST' => "\033[1;33m",      // Negrita + Amarillo
            'PUT' => "\033[1;34m",       // Negrita + Azul
            'DELETE' => "\033[1;31m",    // Negrita + Rojo
            'PATCH' => "\033[1;35m",     // Negrita + Lila
            'HEAD' => "\033[1;32m",      // Negrita + Verde
            'OPTIONS' => "\033[1;95m"    // Negrita + Rosa
        ];

        $reset = "\033[0m"; // Reset de color
        return isset($colors[$method]) ? $colors[$method] . $string . $reset : $string;
    }

    public static function blackyMethod($string)
    {
        return "\033[1;4m" . $string . "\033[0m";
    }

    public static function listRoutesCommand(): void
    {

        $list = [];

        foreach (self::$beforeRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $list[] = [
                    'method' => $method,
                    'url' => $route['pattern'],
                    'fn' => $route['fn'],
                    'middleware' => true
                ];
            }
        }

        foreach (self::$afterRoutes as $method => $routes) {
            foreach ($routes as $route)
                $list[] = [
                    'method' => $method,
                    'url' => $route['pattern'],
                    'fn' => $route['fn'],
                    'middleware' => false
                ];
        }

        usort($list, function ($a, $b) {
            return strcmp($a['url'], $b['url']);
        });

        $longestMethod = max(array_map(fn($a) => strlen($a['method'] . ''. ($a['middleware'] ? ' (M)' : '')), $list));

        $longestUrl = max(array_map(fn($a) => strlen($a['url']), $list));

        $currentRoute = '';
        foreach ($list as $route) {
            if ($currentRoute != $route['url']) {
                $currentRoute = $route['url'];
                Console::show(PHP_EOL . self::blackyMethod($route['url']), Console::PROGRESS);
            }
            Console::show(
                self::colorizeMethod($route['method'], str_pad($route['method'] . ''. ($route['middleware'] ? ' (M)' : ''), $longestMethod, ' ')) . "  " .
                (is_string($route['fn']) ? $route['fn'] : 'function'),
                Console::PROGRESS
            );
        }

    }
}
