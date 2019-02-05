<?php
namespace Cavesman;

use \Cavesman\Router;

class modules extends Display
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
        $router = self::getInstance("\Cavesman\Router");
        if (is_dir(_MODULES_)) {
            $directories = scandir(_MODULES_);
            foreach ($directories as $directory) {
                $module = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config           = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        require_once _MODULES_ . "/" . $directory . "/" . $module . ".php";
                        foreach (glob(_MODULES_ . "/" . $directory . "/entity/*.php") as $filename) {
                            include_once $filename;
                        }
                    }
                }
            }
            foreach ($directories as $directory) {
                $modules = self::getInstance("\Cavesman\Modules");
                $module  = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config           = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        $modules->list[]  = $config;
                        $namespace        = 'Cavesman\\Modules\\' . $module;
                        $modules->$module = self::getInstance($namespace);
                        if (method_exists("\\Cavesman\\Modules\\" . $module, "Smarty")) {
                            $namespace::Smarty();
                        }
                        $router->mount("/" . $module, function() use ($router, $module, $namespace)
                        {
                            $router->get("/(\w+)", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "Action";
                                if (method_exists($namespace, $fn)) {
                                    Display::response($namespace::$fn(), "json");
                                }
                            });
                            $router->post("/(\w+)", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "Action";
                                if (method_exists($namespace, $fn)) {
                                    Display::response($namespace::$fn(), "json");
                                }
                            });
                        });
                        if (method_exists($namespace, "loadRoutes"))
                            $modules->$module->loadRoutes();

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
                $module_name        = $module['module'];
                $namespace          = 'Cavesman\\Modules\\' . $module_name;
                $this->$module_name = new $namespace();
                $hook_name          = "hook" . $hook;
                if (method_exists($this->$module_name, $hook_name) && $module['active'])
                    $html .= $this->$module_name->$hook_name();
            }
        }
        return $html;
    }
}
