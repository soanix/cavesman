<?php
namespace Cavesman;

use Cavesman\Router;

class Modules extends Display
{
    public static $instance;
    public static $list = array();
    function __construct()
    {
        parent::__construct();
        if (defined("_APP_")) {
            if (!is_dir(_WEB_ . "/img"))
                mkdir(_WEB_ . "/img/");
            if (!is_dir(_WEB_ . "/img/m"))
                mkdir(_WEB_ . "/img/m");
        }
    }


    public static function loadModules()
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
                        foreach (glob(_MODULES_ . "/" . $directory . "/entity/*.php") as $filename) {
                            require_once $filename;
                        }
                    }
                }
            }
            foreach ($directories as $directory) {

                $module  = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config           = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        self::$list[]  = $config;
                        $namespace        = 'src\\Modules\\' . ucfirst($module);
                        $modules->$module = self::getInstance($namespace);
                        if (method_exists($namespace, "Smarty")) {
                            $namespace::Smarty();
                        }
                        self::$router->mount("/" . strtolower($module), function() use ($module, $namespace)
                        {
                            self::$router->get("/", function($fn) use ($module, $namespace)
                            {
                                $fn = $module . "Page";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "HTML");
                                }
                            });
                            self::$router->get("/(\w+)", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "ViewAction";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "json");
                                }
                            });
                            self::$router->post("/(\w+)", function($fn) use ($module, $namespace)
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
        $modules = self::getInstance(self::class);
        if ($hook) {
            foreach (self::$list as $module) {
                $namespace          = 'src\\Modules\\' . $module['module'];
                $hook_name          = "hook" . str_replace(" ", "", ucwords(str_replace("_", " ", $hook)));
                if (method_exists($namespace, $hook_name) && $module['active'])
                    $html .= $namespace::$hook_name();
            }
        }
        return $html;
    }
}
