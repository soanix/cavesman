<?php

namespace Cavesman;

use src\Modules\Lang;

class Modules extends Display
{
    public static $instance;

    // Define list to put all modules
    public static $list = array();

    public static function __install()
    {
        parent::__install();
    }

    /**
     * Load all modules in src/modules
     */
    public static function loadModules(): void
    {
        $modules = self::getInstance(self::class);
        if (is_dir(_MODULES_)) {
            $directories = scandir(_MODULES_);
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        require_once _MODULES_ . "/" . $directory . "/" . $module . ".php";

                        if (is_dir(_MODULES_ . "/" . $directory . "/controller")) {
                            foreach (glob(_MODULES_ . "/" . $directory . "/controller/*.php") as $filename) {
                                require_once $filename;
                            }
                        }

                        if (is_dir(_MODULES_ . "/" . $directory . "/abstract")) {
                            foreach (glob(_MODULES_ . "/" . $directory . "/abstract/*.php") as $filename) {
                                require_once $filename;
                            }
                        }

                        if (is_dir(_MODULES_ . "/" . $directory . "/entity")) {
                            foreach (glob(_MODULES_ . "/" . $directory . "/entity/*.php") as $filename) {
                                require_once $filename;
                            }
                        }
                    }
                }
            }
            foreach ($directories as $directory) {

                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config = Config::get("modules." . $directory, $config);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        if (is_dir(_MODULES_ . "/" . $directory . "/controller")) {
                            self::$list[$config['name']] = $config;
                            $namespace = [];
                            foreach (glob(_MODULES_ . "/" . $directory . "/controller/*.php") as $filename) {
                                $controller = pathinfo($filename);
                                $c_name = $controller['filename'];
                                $namespace[$c_name] = 'src\\Modules\\' . self::parseClassName($module) . "\\Controller\\" . self::parseClassName($c_name);

                                $namespace[$c_name]::$config = self::$list[$config['name']];

                                // INSTALL MODULE INIT OPTIONS
                                if (method_exists($namespace[$c_name], "__update"))
                                    $namespace[$c_name]::__update();
                                // INSTALL MODULE INIT OPTIONS
                                if (method_exists($namespace[$c_name], "__install"))
                                    $namespace[$c_name]::__install();

                                // MENU INIT OPTIONS
                                if (method_exists($namespace[$c_name], "menu"))
                                    Menu::addItem($namespace[$c_name]::menu());

                                // LOAT ROUTER FUNCTION FROM MODULE
                                if (method_exists($namespace[$c_name], "router")) {
                                    $namespace[$c_name]::router();
                                }

                                Router::mount(_PATH_ . self::trans($c_name . "-slug", [], $module), function () use ($module, $namespace, $c_name) {
                                    Router::middleware("POST|GET", "/(.*)", function ($fn) use ($module, $namespace, $c_name) {
                                        if (method_exists($namespace[$c_name], "Smarty")) {
                                            $namespace[$c_name]::Smarty();
                                        }
                                    });

                                });
                            }

                        } else {


                            self::$list[$config['name']] = $config;

                            $namespace = 'src\\Modules\\' . self::parseClassName($module);
                            //$modules->$module = self::getInstance($namespace);

                            $namespace::$config = self::$list[$config['name']];


                            if (method_exists($namespace, "Smarty")) {
                                $namespace::Smarty();
                            }

                            // INSTALL MODULE INIT OPTIONS
                            if (method_exists($namespace, "__install"))
                                $namespace::__install();

                            // MENU INIT OPTIONS
                            if (method_exists($namespace, "menu"))
                                Menu::addItem($namespace::menu());

                            // LOAT ROUTER FUNCTION FROM MODULE
                            if (method_exists($namespace, "router")) {
                                $namespace::router();
                            }

                        }
                    }

                }
            }
        }
    }

    public static function parseClassName($name)
    {
        $name = explode("_", $name);
        $name = array_map(function ($string) {
            return ucfirst(mb_strtolower($string));
        }, $name);
        return implode('', $name);
    }

    /**
     * Translate multilanguage support function
     * @param string $string string to translate
     * @param array $binds array with strings to sustitute
     * @param string $modules module from translate comeback
     * @return string          string translated or parsed
     */
    public static function trans(string $string = '', array $binds = [], string $modules = ''): string
    {
        if (class_exists(Lang::class)) {
            if ($modules)
                return Lang::l($string, $binds, $modules);
            if (isset(get_called_class()::$config['name']))
                return Lang::l($string, $binds, get_called_class()::$config['name']);
            return Lang::l($string, $binds);
        } else {
            $binded = $string;
            foreach ($binds as $key => $value) {
                $binded = str_replace($key, $value, $binded);
            }
            return $binded;
        }
    }

    /**
     * Smarty load hooks from module functions
     * @param boolean $hook name of hook
     * @return string html content from hook
     */
    function hooks($hook = false): string
    {
        $html = '';
        $modules = self::getInstance(self::class);
        if ($hook) {
            foreach (self::$list as $module) {
                $namespace = 'src\\Modules\\' . self::parseClassName($module['module']);
                $hook_name = "hook" . str_replace(" ", "", ucwords(str_replace("_", " ", $hook)));
                if (method_exists($namespace, $hook_name) && $module['active'])
                    $html .= $namespace::$hook_name();
            }
        }
        return $html;
    }

}
