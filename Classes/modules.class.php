<?php
namespace Cavesman;

use Cavesman\Router;

class Modules extends Display
{
    public static $instance;
    public $list = array();
    function __construct()
    {
        parent::__construct();
        if (defined("_APP_")) {
            if (!is_dir(_APP_ . "/img"))
                mkdir(_APP_ . "/img/");
            if (!is_dir(_APP_ . "/img/m"))
                mkdir(_APP_ . "/img/m");
        }
    }


    public static function loadModules()
    {
        $router = self::getInstance(Router::class);
        if (is_dir(_MODULES_)) {
            $directories = scandir(_MODULES_);
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config           = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        require_once _MODULES_ . "/" . $directory . "/" . $module . ".php";
                        foreach (glob(_MODULES_ . "/" . $directory . "/Entity/*.php") as $filename) {
                            require_once $filename;
                        }
                    }
                }
            }
            foreach ($directories as $directory) {
                $modules = self::getInstance(self::class);
                $module  = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config           = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        $modules->list[]  = $config;
                        $namespace        = 'app\\Modules\\' . $module;
                        $modules->$module = self::getInstance($namespace);
                        if (method_exists($namespace, "Smarty")) {
                            $namespace::Smarty();
                        }
                        $router->mount("/" . strtolower($module), function() use ($router, $module, $namespace)
                        {
                            $router->get("/(\w+)", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "ViewAction";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "json");
                                }
                            });
                            $router->post("/(\w+)", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "Action";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "json");
                                }
                            });
                        });
                        if (method_exists($namespace, "loadRoutes"))
                            $namespace::loadRoutes();

                    }
                }
            }
        }
    }

    function hooks($hook = false)
    {
        $html = '';
        if ($hook) {
            foreach ($this->list as $module) {
                $namespace          = 'app\\Modules\\' . $module['module'];
                $hook_name          = "hook" . str_replace(" ", "", ucwords(str_replace("_", " ", $hook)));
                if (method_exists($namespace, $hook_name) && $module['active'])
                    $html .= $namespace::$hook_name();
            }
        }
        return $html;
    }
}
