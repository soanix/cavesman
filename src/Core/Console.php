<?php

namespace Cavesman;


use Cavesman\Enum\Directory;
use DateInterval;
use DateMalformedIntervalStringException;
use DateTime;
use ReflectionException;
use ReflectionMethod;

/**
 * @author      Bram(us) Van Damme <bramus@bram.us>, Pedro Soanix Oliver <soanix91@gmail.com>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

/**
 * Class Console.
 */
class Console
{

    const string ERROR = 'error';
    const string WARNING = 'warning';
    const string SUCCESS = 'success';
    const string INFO = 'info';
    const string PROGRESS = 'progress';
    /**
     * @var string Log string of all display
     */
    public static string $log = '';
    /**
     * @var float Last percent recorded
     */
    public static float $lastPercent = -1;
    /**
     * @var ?DateTime Start Time
     */
    public static ?DateTime $startProgress = null;
    /**
     * @var ?DateTime Last update time
     */
    public static ?DateTime $lastUpdate = null;
    /**
     * @var array List of errors
     */
    public static array $errors = [];
    /**
     * @var string Current job in progress
     */
    public static string $currentProgress = 'Generic';
    /**
     * @var bool List of errors
     */
    public static ?bool $updateAlways = null;
    /**
     * @var ?int List of errors
     */
    public static ?int $percentPrecision = null;
    /**
     * @var bool Enable Log
     */
    public static ?bool $logEnabled = null;
    /**
     * @var bool Enable Log
     */
    public static ?bool $debug = null;
    /**
     * @var array [object|callable] The function to be executed when no route has been matched
     */
    protected static array $notFoundCallback = [];
    /**
     * @var array The route patterns and their handling functions
     */
    private static array $afterRoutes = [];
    /**
     * @var array The before middleware route patterns and their handling functions
     */
    private static array $beforeRoutes = [];
    /**
     * @var string Current base route, used for (sub)route mounting
     */
    private static string $baseRoute = '';
    /**
     * @var string The Request Method that needs to be handled
     */
    private static string $requestedMethod = 'COMMAND';
    /**
     * @var ?string The Server Base Path for Router Execution
     */
    private static ?string $serverBasePath = null;
    /**
     * @var string Default Controllers Namespace
     */
    private static string $namespace = '';

    public static function clear(): void
    {
        self::$baseRoute = '';
        self::$requestedMethod = '';
        self::$serverBasePath = null;
        self::$afterRoutes = [];
        self::$beforeRoutes = [];
        self::$notFoundCallback = [];
        self::$namespace = '';
    }

    public static function listRoutesCommand(): void
    {
        $list = [];

        foreach (self::$afterRoutes as $routes)
            foreach ($routes as $route)
                $list[] = [
                    'url' => $route['pattern'],
                    'fn' => $route['fn'],
                    'description' => $route['description'],
                ];

        usort($list, function ($a, $b) {
            return strcmp($a['url'], $b['url']);
        });
        $longestUrl = max(array_map(fn($a) => strlen($a['url']), $list));
        foreach ($list as $route) {
            Console::show(
                str_pad($route['url'], $longestUrl) . "\t" .
                $route['description'],
                Console::PROGRESS
            );
        }
    }

    public static function show($message = '', $type = '', $exit = false)
    {
        self::print($message, $type, $exit);
    }

    /**
     * @param string $message
     * @param string $type
     * @param false $exit
     * @return void
     */
    public static function print(string $message = '', string $type = '', false $exit = false): void
    {
        self::log($message, $type);

        $text = '';
        if (PHP_SAPI !== 'cli')
            return;
        if (!in_array($type, [self::PROGRESS, self::INFO]))
            $text .= "[" . new DateTime()->format('Y-m-d H:i:s') . "]";
        switch ($type) {
            case self::PROGRESS:
            case self::INFO:
                $text .= $message;
                break;

            case self::ERROR:
                self::$errors[] = $text . " " . $message;
                $text .= "\e[0;31m[ERROR] \e[m\t" . $message;

                break;
            case self::WARNING:
                $text .= "\e[0;33m[WARNING] \e[m\t" . $message;
                break;
            case self::SUCCESS:
                $text .= "\e[0;32m[SUCCESS] \e[m\t" . $message;
                break;
            default:
                $text .= "\e[1;36m[GENERAL] \e[m\t" . $message;
        }

        $text .= PHP_EOL;

        if (!in_array($type, [self::PROGRESS, self::INFO]))
            self::$log .= $text;

        self::$debug = !is_null(self::$debug) ? self::$debug : Config::get('params.log.debug', true);

        if ($text && (self::$debug || in_array($type, [self::PROGRESS, self::INFO])))
            echo $text;

        if ($exit)
            exit();
    }

    private static function log($message, $type): void
    {
        self::$logEnabled = !is_null(self::$logEnabled) ? self::$logEnabled : Config::get('params.log.enabled', true);

        if (!self::$logEnabled) {
            return;
        }

        if (in_array($type, [self::PROGRESS, self::INFO])) {
            return;
        }

        $text = "[" . new DateTime()->format('Y-m-d H:i:s') . "]";

        $text .= match ($type) {
            self::ERROR => "[ERROR] \t" . $message,
            self::WARNING => "[WARNING] \t" . $message,
            self::SUCCESS => "[SUCCESS] \t" . $message,
            default => "[INFO]\t" . $message,
        };

        $text .= PHP_EOL;

        if (!is_dir(FileSystem::getPath(Directory::LOG)))
            mkdir(FileSystem::getPath(Directory::LOG), 0777, true);

        $fp = @fopen(FileSystem::getPath(Directory::LOG). '/' . date('d-m-Y') . '.log', 'a+');
        @fwrite($fp, $text);
        @fclose($fp);
    }

    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function middleware(string $methods, string $pattern, callable|object|string $fn): void
    {
        $pattern = self::$baseRoute . trim($pattern, ':');
        $pattern = self::$baseRoute ? rtrim($pattern, ':') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
            );
        }
    }

    /**
     * Shorthand for a route accessed using COMMAND.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function command(string $pattern, callable|object|string $fn, $description = ''): void
    {
        if (php_sapi_name() === 'cli') {
            self::match('COMMAND', $pattern, $fn, $description);
        }
    }

    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     * @param string $description
     */
    public static function match(string $methods, string $pattern, callable|object|string $fn, string $description = ''): void
    {
        $pattern = self::$baseRoute . trim($pattern, ':');
        $pattern = self::$baseRoute ? rtrim($pattern, ':') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$afterRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
                'description' => $description
            );
        }
    }

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string $baseRoute The route sub pattern to mount the callbacks on
     * @param callable|object|string $fn The callback method
     */
    public static function mount(string $baseRoute, callable|object|string $fn): void
    {
        // Track current base route
        $curBaseRoute = self::$baseRoute;

        // Build new base route string
        self::$baseRoute .= $baseRoute;

        // Call the callable
        call_user_func($fn);

        // Restore original base route
        self::$baseRoute = $curBaseRoute;
    }

    /**
     * Get all request headers.
     *
     * @return array The request headers
     */
    public static function getRequestHeaders(): array
    {

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }


        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if ((str_starts_with($name, 'HTTP_')) || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(
                    [' ', 'Http'],
                    ['-', 'HTTP'],
                    ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                )] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account.
     *
     * @return string The Request method to handle
     */
    public static function getRequestMethod(): string
    {
        return 'COMMAND';
    }

    /**
     * Execute the router: Loop all defined before middleware's and routes, and execute the handling function if a match was found.
     *
     * @param callable|object|string|null $callback Function to be executed after a matching route was handled (= after router middleware)
     *
     * @return bool
     */
    public static function run(callable|object|string|null $callback = null): bool
    {
        // Handle all before middlewares
        if (isset(self::$beforeRoutes[self::$requestedMethod])) {
            self::handle(self::$beforeRoutes[self::$requestedMethod]);
        }


        // Handle all routes
        $numHandled = 0;
        if (isset(self::$afterRoutes[self::$requestedMethod])) {
            $numHandled = self::handle(self::$afterRoutes[self::$requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            self::trigger404();
        } // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    /**
     * Handle a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes Collection of route patterns and their handling functions
     * @param bool $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     */
    private static function handle(array $routes, bool $quitAfterRun = false): int
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $uri = self::getCurrentUri();

        // Loop all routes
        foreach ($routes as $route) {

            // get routing matches
            $is_match = self::patternMatches($route['pattern'], $uri, $matches);

            // is there a valid match?
            if ($is_match) {

                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);

                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function ($match, $index) use ($matches) {

                    // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        if ($matches[$index + 1][0][1] > -1) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), ':');
                        }
                    } // We have no following parameters: return the whole lot

                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], ':') : null;
                }, $matches, array_keys($matches));


                // Call the handling function with the URL parameters if the desired input is callable
                self::invoke($route['fn'], $params);

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }

    /**
     * Define the current relative URI.
     *
     * @return string
     */
    public static function getCurrentUri(): string
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = $argv[1] ?? ($_SERVER['argv'][1] ?? '');

        // Remove trailing slash + enforce a slash at the start
        return trim($uri, ':');
    }

    /**
     * Replace all curly braces matches {} into word patterns (like Laravel)
     * Checks if there is a routing match
     *
     * @param $pattern
     * @param $uri
     * @param $matches
     * @return bool -> is match yes/no
     */
    private static function patternMatches($pattern, $uri, &$matches): bool
    {
        // Replace all curly braces matches {} into word patterns (like Laravel)
        $pattern = preg_replace('/:{(.*?)}/', ':(.*?)', $pattern);

        // we may have a match!
        return boolval(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE));
    }

    /**
     * @param $fn
     * @param array $params
     * @return void
     */
    private static function invoke($fn, array $params = []): void
    {

        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } // If not, check the existence of special parameters
        elseif (stripos($fn, '@') !== false) {
            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);

            // Adjust controller class if namespace has been set
            if (self::getNamespace() !== '') {
                $controller = self::getNamespace() . '\\' . $controller;
            }

            try {

                $reflectedMethod = new ReflectionMethod($controller, $method);
                // Make sure it's callable
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {


                    if ($reflectedMethod->isStatic()) {
                        forward_static_call_array(array($controller, $method), $params);
                    } else {
                        // Make sure we have an instance, because a non-static method must not be called statically
                        if (is_string($controller)) {
                            $controller = new $controller();
                        }
                        call_user_func_array(array($controller, $method), $params);
                    }
                }
            } catch (ReflectionException $reflectionException) {
                echo $reflectionException->getMessage();
                // The controller class is not available or the class does not have the method $method
            }
        }
    }

    /**
     * Get the given Namespace before.
     *
     * @return string The given Namespace if exists
     */
    public static function getNamespace(): string
    {
        return self::$namespace;
    }

    /**
     * Set a Default Lookup Namespace for Callable methods.
     *
     * @param string $namespace A given namespace
     */
    public static function setNamespace(string $namespace): void
    {
        self::$namespace = $namespace;
    }

    /**
     * Triggers 404 response
     */
    public static function trigger404(): void
    {

        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // handle 404 pattern
        if (count(self::$notFoundCallback) > 0) {
            // loop fallback-routes
            foreach (self::$notFoundCallback as $route_pattern => $route_callable) {

                // matches result
                $matches = [];

                // check if there is a match and get matches as $matches (pointer)
                $is_match = self::patternMatches($route_pattern, self::getCurrentUri(), $matches);

                // is fallback route match?
                if ($is_match) {

                    // Rework matches to only contain the matches, not the orig string
                    $matches = array_slice($matches, 1);

                    // Extract the matched URL parameters (and only the parameters)
                    array_map(function ($match, $index) use ($matches) {

                        // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                        if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                            if ($matches[$index + 1][0][1] > -1) {
                                return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), ':');
                            }
                        } // We have no following parameters: return the lot

                        return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], ':') : null;
                    }, $matches, array_keys($matches));

                    self::invoke($route_callable);

                    ++$numHandled;
                }
            }
            if ($numHandled == 0 and self::$notFoundCallback[':']) {
                self::invoke(self::$notFoundCallback[':']);
            } elseif ($numHandled == 0) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }
    }

    /**
     * Set the 404 handling function.
     *
     * @param callable|object|string $match_fn The function to be executed
     * @param callable|object|string|null $fn The function to be executed
     */
    public static function set404(callable|object|string $match_fn, callable|object|string|null $fn = null): void
    {
        if (!is_null($fn)) {
            self::$notFoundCallback[$match_fn] = $fn;
        } else {
            self::$notFoundCallback[':'] = $match_fn;
        }
    }

    /**
     * Return server base Path, and define it if isn't defined.
     *
     * @return string
     */
    public static function getBasePath()
    {
        // Check if server base path is defined, if not define it.
        if (self::$serverBasePath === null) {
            self::$serverBasePath = implode(':', array_slice(explode(':', $_SERVER['SCRIPT_NAME']), 0, -1)) . ':';
        }

        return self::$serverBasePath;
    }

    /**
     * Explicilty sets the server base path. To be used when your entry script path differs from your entry URLs.
     * @see https://github.com/bramus/router/issues/82#issuecomment-466956078
     *
     * @param string $serverBasePath
     */
    public static function setBasePath(string $serverBasePath): void
    {
        self::$serverBasePath = $serverBasePath;
    }

    /**
     * @param int $current Current
     * @param int $total
     * @return void
     * @throws DateMalformedIntervalStringException
     */
    public static function progress(int $current, int $total): void
    {

        self::$updateAlways = !is_null(self::$updateAlways) ? self::$updateAlways : Config::get('params.console.progress.update_always', false);
        self::$percentPrecision = !is_null(self::$percentPrecision) ? self::$percentPrecision : Config::get('params.console.progress.percent.precision', 0);

        $percent = round(($current / $total) * 100, self::$percentPrecision);

        if (!self::$startProgress)
            self::$startProgress = new DateTime();
        if (!self::$lastUpdate)
            self::$lastUpdate = new DateTime();
        if (self::$updateAlways || self::$lastPercent !== $percent) {
            $since_start = new DateTime()->diff(self::$startProgress);

            self::$lastUpdate = new DateTime();

            self::$lastPercent = $percent;

            $seconds = $since_start->s + ($since_start->i * 60) + ($since_start->h * 60 * 60);

            if ($seconds > 0 && $current > 0) {
                $seconds = ($seconds * ($total - $current)) / $current;
            } else {
                $seconds = 0;
            }

            $estimated = new DateTime()->add(new DateInterval('PT' . round(abs($seconds)) . 'S'));

            $diffEnd = new DateTime()->diff($estimated);

            Console::clean();

            self::print("\e[0;33mProcessing\e[m" . str_pad('', $percent % 2 == 0 ? 3 : 1, '.', STR_PAD_LEFT), self::PROGRESS);

            $formatedDuration = '';

            if ($since_start->d)
                $formatedDuration .= $since_start->d . "days ";
            if ($since_start->h)
                $formatedDuration .= $since_start->h . "h ";
            if ($since_start->i)
                $formatedDuration .= $since_start->i . "m ";
            if ($since_start->s)
                $formatedDuration .= $since_start->s . "s";


            $formatEnd = '';

            if ($diffEnd->y)
                $formatEnd .= $diffEnd->y . "years ";
            if ($diffEnd->m)
                $formatEnd .= $diffEnd->m . "month ";
            if ($diffEnd->d)
                $formatEnd .= $diffEnd->d . "days ";
            if ($diffEnd->h)
                $formatEnd .= $diffEnd->h . "h ";
            if ($diffEnd->i)
                $formatEnd .= $diffEnd->i . "m ";
            if ($diffEnd->s)
                $formatEnd .= $diffEnd->s . "s";

            self::print("\e[0;33mCurrent job:\e[m " . self::$currentProgress, self::PROGRESS);
            self::print("\e[0;33mRunning time:\e[m " . $formatedDuration, self::PROGRESS);
            self::print("\e[0;35mEstimated time duration:\e[m " . $formatEnd, self::PROGRESS);
            self::print("\e[0;35mEstimated date end:\e[m " . $estimated->format('d-m-Y H:i:s'), self::PROGRESS);
            self::print("\e[0;33mItems processed:\e[m " . $current, self::PROGRESS);
            self::print("\e[0;33mPending Items:\e[m " . $total - $current, self::PROGRESS);
            self::print("\e[0;33mTotal Items:\e[m " . $total, self::PROGRESS);
            self::print("\e[0;33mMemory:\e[m " . round(memory_get_usage() / 1048576, 2) . "MB", self::PROGRESS);
            self::print("\e[0;32mCompleted:\e[m " . $percent . "%", self::PROGRESS);


            $left = round($percent / 2, self::$percentPrecision) - 1;
            $right = 50 - $left - 1;

            self::print("\e[0;32m[\e[m" . str_pad('', $left, "=", STR_PAD_LEFT) . ">" . "\e[m" . str_pad('', $right, "·", STR_PAD_LEFT) . "\e[0;32m]\e[m", self::PROGRESS);

            foreach (self::$errors as $error) {
                self::print("\e[0;33m[ERROR]\e[m\t " . $error, self::PROGRESS);
            }

        }

    }

    /**
     * @return void
     */
    public static function clean(): void
    {
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    /**
     * Request value to cli
     *
     * @param $name
     * @return string
     */
    public static function requestValue($name): string
    {
        self::show($name, Console::INFO);
        return trim(fgets(STDIN));
    }

    public static function setCurrentJob($currentProgress)
    {
        self::$currentProgress = $currentProgress;
    }
}
