<?php
namespace Cavesman;

class Modules extends Display
{
    public static $instance;

    // Define list to put all modules
    public static $list = array();

    public static function __install()
    {
        parent::__install();
        if (defined("_APP_")) {
            if (!is_dir(_WEB_ . "/img"))
                mkdir(_WEB_ . "/img/");
            if (!is_dir(_WEB_ . "/img/m"))
                mkdir(_WEB_ . "/img/m");
        }
    }
    /**
     * Load all modules in src/modules
     */
    public static function loadModules() : void
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
                    $config = array_merge($config, Config::get("params", ["modules", "albaran"]));
                    if ($config['active']) {
                        self::$list[$config['name']]  = $config;

                        $namespace  = 'src\\Modules\\' . ucfirst($module);
                        //$modules->$module = self::getInstance($namespace);

                        $namespace::$config = self::$list[$config['name']];


                        if (method_exists($namespace, "Smarty")) {
                            $namespace::Smarty();
                        }
                        self::$router->get("/css/(\w+).css", function($fn) use ($module, $namespace)
                        {
                            $fn = $fn . "CssViewAction";
                            if (method_exists($namespace, $fn)) {
                                self::response($namespace::$fn(), "css");
                            }
                        });
                        self::$router->get("/js/(\w+).js", function($fn) use ($module, $namespace)
                        {
                            $fn = $fn . "JsViewAction";
                            if (method_exists($namespace, $fn)) {
                                self::response($namespace::$fn(), "js");
                            }
                        });
                        if(method_exists($namespace, "loadRoutes")){
                            self::$router->mount("/" . $namespace::trans($namespace::$config['name']."-slug"), function() use ($module, $namespace){
                                self::$router->before("GET", "*", function() use ($module, $namespace){
                                    self::$smarty->assign("page", $namespace::$config['name']);
                                    self::$smarty->assign("module", $namespace::$config['name']);
                                    self::$smarty->assign("module_dir", _MODULES_."/".$namespace::$config['name']);

                                    /* DEFINIR AQUI */
                                });
                            });
                        }
                        // INSTALL MODULE INIT OPTIONS
                        if (method_exists($namespace, "__install"))
                            $namespace::__install();

                        // MENU INIT OPTIONS
                        if (method_exists($namespace, "menu"))
                            Menu::addItem($namespace::menu());

                        // LOAT ROUTER FUNCTION FROM MODULE
                        if (method_exists($namespace, "loadRoutes")){
                            $namespace::loadRoutes();
                            self::$router->before("GET", _PATH_ . $namespace::$config['name']."/.*", function() use ($module, $namespace){
                                self::$smarty->assign("page", $namespace::$config['name']);
                                self::$smarty->assign("module", $namespace::$config['name']);
                                self::$smarty->assign("module_dir", _MODULES_."/".$namespace::$config['name']);
                            });
                        }

                        self::$router->mount(_PATH_ . $namespace::$config['name'], function() use ($module, $namespace)
                        {
                            self::$router->before("POST|GET", "/(.*)", function($fn) use ($module, $namespace)
                            {
                                self::$smarty->assign("page", $module);
                                self::$smarty->assign("module_dir", _MODULES_."/".$module);
                            });
                            self::$router->get("/", function() use ($module, $namespace)
                            {
                                $fn = $module . "ViewPage";
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


                    }
                }
            }
            self::$smarty->assign("modules", self::$list);
        }
    }

    /**
     * Smarty load hooks from module functions
     * @param  boolean $hook name of hook
     * @return string html content from hook
     */
    function hooks($hook = false) : string
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

    /**
     * Translate multilanguage support function
     * @param  string $string  string to translate
     * @param  array  $binds   array with strings to sustitute
     * @param  string $modules module from translate comeback
     * @return string          string translated or parsed
     */
    public static function trans(string $string = '', array $binds = [], string $modules = '') : string
    {
        if(class_exists(\src\Modules\Lang::class)){
            if(isset(get_called_class()::$config['name']))
                return \src\Modules\Lang::l($string, $binds, get_called_class()::$config['name']);
            return \src\Modules\Lang::l($string, $binds);
        } else {
            $binded = $string;
			foreach($binds as $key => $value){
				$binded = str_replace($key, $value, $binded);
			}
            return $binded;
        }
    }

}
