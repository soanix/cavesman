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

                        if(is_dir(_MODULES_ . "/" . $directory . "/controller")){
                            foreach (glob(_MODULES_ . "/" . $directory . "/controller/*.php") as $filename) {
                                require_once $filename;
                            }
                        }

                        if(is_dir(_MODULES_ . "/" . $directory . "/abstract")){
                            foreach (glob(_MODULES_ . "/" . $directory . "/abstract/*.php") as $filename) {
                                require_once $filename;
                            }
                        }

                        if(is_dir(_MODULES_ . "/" . $directory . "/entity")){
                            foreach (glob(_MODULES_ . "/" . $directory . "/entity/*.php") as $filename) {
                                require_once $filename;
                            }
                        }
                    }
                }
            }
            foreach ($directories as $directory) {

                $module  = str_replace('directory/', '', $directory);
                if ($module !== '.' && $module != '..') {
                    $config = json_decode(file_get_contents(_MODULES_ . "/" . $directory . "/config.json"), true);
                    $config = Config::get("modules.".$directory, $config);
                    $config['module'] = $directory;
                    if ($config['active']) {
                        if(is_dir(_MODULES_ . "/" . $directory."/controller")){
                            self::$list[$config['name']]  = $config;
                            $namespace = [];
                            foreach (glob(_MODULES_ . "/" . $directory . "/controller/*.php") as $filename) {
                                $controller = pathinfo($filename);
                                $c_name = $controller['filename'];
                                $namespace[$c_name]  = 'src\\Modules\\' . ucfirst($module)."\\Controller\\".ucFirst($c_name);
                                //$modules->$module = self::getInstance($namespace);

                                $namespace[$c_name]::$config = self::$list[$config['name']];


                                Router::get("/css/(\w+).css", function($fn) use ($module, $namespace, $c_name)
                                {
                                    $fn = $fn . "CssViewAction";
                                    if (method_exists($namespace[$c_name], $fn)) {
                                        self::response($namespace[$c_name]::$fn(), "css");
                                    }
                                });
                                Router::get("/js/(\w+).js", function($fn) use ($module, $namespace, $c_name)
                                {
                                    $fn = $fn . "JsViewAction";
                                    if (method_exists($namespace[$c_name], $fn)) {
                                        self::response($namespace[$c_name]::$fn(), "js");
                                    }
                                });
                                if(method_exists($namespace, "router")){
                                    Router::mount(_PATH_ . $namespace::trans($c_name."-slug"), function() use ($module, $namespace, $c_name){
                                       Router::middleware("GET", "*", function() use ($module, $namespace, $c_name){
                                            self::$smarty->assign("page", $c_name);
                                            self::$smarty->assign("module", $namespace[$c_name]::$config['name']);
                                            self::$smarty->assign("module_dir", _MODULES_."/".$namespace[$c_name]::$config['name']);

                                            /* DEFINIR AQUI */
                                        });
                                    });
                                }
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
                                if (method_exists($namespace[$c_name], "router")){
                                    $namespace[$c_name]::router();
                                    Router::middleware("GET", _PATH_ . $c_name."/.*", function() use ($module, $namespace, $c_name){
                                        self::$smarty->assign("page", $c_name);
                                        self::$smarty->assign("module", $namespace[$c_name]::$config['name']);
                                        self::$smarty->assign("module_dir", _MODULES_."/".$namespace[$c_name]::$config['name']);
                                    });
                                }

                                Router::mount(_PATH_ . $c_name, function() use ($module, $namespace, $c_name)
                                {

                                    Router::middleware("POST|GET", "/(.*)", function($fn) use ($module, $namespace, $c_name)
                                    {
                                        self::$smarty->assign("page", $c_name);
                                        self::$smarty->assign("module_dir", _MODULES_."/".$module);
                                        if (method_exists($namespace[$c_name], "Smarty")) {
                                            $namespace[$c_name]::Smarty();
                                        }
                                    });
                                    Router::get("/", function() use ($module, $namespace, $c_name)
                                    {

                                        $fn = $c_name . "ViewPage";
                                        if (method_exists($namespace[$c_name], $fn)) {
                                            self::response($namespace[$c_name]::$fn(), "HTML");
                                        }
                                    });

                                    Router::get("/(\w+)", function($fn) use ($module, $namespace, $c_name)
                                    {
                                        $fn = $fn . "ViewAction";
                                        if (method_exists($namespace[$c_name], $fn)) {
                                            self::response($namespace[$c_name]::$fn(), "json");
                                        }
                                    });
                                   Router::post("/(\w+)", function($fn) use ($module, $namespace, $c_name)
                                    {
                                        $fn = $fn . "Action";
                                        if (method_exists($namespace[$c_name], $fn)) {
                                            self::response($namespace[$c_name]::$fn(), "json");
                                        }
                                    });
                                });
                            }

                        }else{


                            self::$list[$config['name']]  = $config;

                            $namespace  = 'src\\Modules\\' . ucfirst($module);
                            //$modules->$module = self::getInstance($namespace);

                            $namespace::$config = self::$list[$config['name']];


                            if (method_exists($namespace, "Smarty")) {
                                $namespace::Smarty();
                            }
                            Router::get("/css/(\w+).css", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "CssViewAction";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "css");
                                }
                            });
                            Router::get("/js/(\w+).js", function($fn) use ($module, $namespace)
                            {
                                $fn = $fn . "JsViewAction";
                                if (method_exists($namespace, $fn)) {
                                    self::response($namespace::$fn(), "js");
                                }
                            });
                            if(method_exists($namespace, "router")){
                                Router::mount("/" . $namespace::trans($namespace::$config['name']."-slug"), function() use ($module, $namespace){
                                    Router::middleware("GET", "*", function() use ($module, $namespace){
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
                            if (method_exists($namespace, "router")){
                                $namespace::router();
                               Router::middleware("GET", _PATH_ . $namespace::$config['name']."/.*", function() use ($module, $namespace){
                                    self::$smarty->assign("page", $namespace::$config['name']);
                                    self::$smarty->assign("module", $namespace::$config['name']);
                                    self::$smarty->assign("module_dir", _MODULES_."/".$namespace::$config['name']);
                                });
                            }

                            Router::mount(_PATH_ . $namespace::$config['name'], function() use ($module, $namespace)
                            {
                                Router::middleware("POST|GET", "/(.*)", function($fn) use ($module, $namespace)
                                {
                                    self::$smarty->assign("page", $module);
                                    self::$smarty->assign("module_dir", _MODULES_."/".$module);
                                });
                                Router::get("/", function() use ($module, $namespace)
                                {
                                    $fn = $module . "ViewPage";
                                    if (method_exists($namespace, $fn)) {
                                        self::response($namespace::$fn(), "HTML");
                                    }
                                });

                                Router::get("/(\w+)", function($fn) use ($module, $namespace)
                                {
                                    $fn = $fn . "ViewAction";
                                    if (method_exists($namespace, $fn)) {
                                        self::response($namespace::$fn(), "json");
                                    }
                                });
                                Router::post("/(\w+)", function($fn) use ($module, $namespace)
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
