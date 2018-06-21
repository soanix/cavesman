<?php
namespace Cavesman;

class modules {
    public $list = array();
    public $router;
    function __construct(){
        $this->loadModules();
        if(defined("_APP_")){
            if(!is_dir(_APP_."/img"))
                mkdir(_APP_."/img/");
    		if(!is_dir(_APP_."/img/m"))
                mkdir(_APP_."/img/m");
        }
    }
    function loadSmarty(){
        $this->smarty = new \SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";

    }
    function loadModules(){
        $this->loadSmarty();
        $this->router = new \Bramus\Router\Router();
        if(is_dir(_MODULES_)){
            $directories = scandir(_MODULES_);
            foreach($directories as $directory){
                $module = str_replace('directory/', '', $directory);
                if($module !== '.' && $module != '..'){
    				$config = json_decode(file_get_contents(_MODULES_."/".$directory."/config.json"), true);
					$config['module'] = $directory;
					if($config['active']){
                        $this->list[] = $config;
                    	require_once _MODULES_."/".$directory."/".$module.".php";
                        $namespace = 'Cavesman\\Modules\\'.$module;
    					$this->$module = new $namespace();
                        $this->router->mount("/".$module, function() use ($module){
                            $this->router->get("/(\w+)", function($fn) use ($module){
                                $fn = "action".$fn;
                                if(method_exists($this->$module, $fn)){
                                    Display::response($this->$module->$fn(), "json");
                                }
                            });
                            $this->router->post("/(\w+)", function($fn) use ($module){
                                $fn = "action".$fn;
                                if(method_exists($this->$module, $fn)){
                                    Display::response($this->$module->$fn(), "json");
                                }
                            });
                        });
                        if(method_exists($namespace, "loadRoutes"))
                            $this->router = $this->$module->loadRoutes($this->router);
    				}
                }
            }
        }
    }
    function hooks($hook = false){
        $html = '';
        if($hook){
            foreach($this->list as $module){
                $module_name = $module['module'];
				$namespace = 'Cavesman\\Modules\\'.$module_name;
                $this->$module_name = new $namespace();
                $hook_name = "hook".$hook;
                if(method_exists($this->$module_name, $hook_name) && $module['active'])
                    $html .= $this->$module_name->$hook_name();
            }
        }
        return $html;
    }
	function p($value = '', $default = ''){
        return isset($_POST[$value]) ? $_POST[$value] : $default;
    }
    function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
}
?>
