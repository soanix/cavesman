<?

class modules {
    public $list = array();
    public $router;
    function __construct(){
        $this->loadModules();
		if(!is_dir(_APP_."/img/m"))
            mkdir(_APP_."/img/m");
    }
    function loadSmarty(){
        $this->smarty = new SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";

    }
    function loadModules(){
        $this->loadSmarty();
        $this->router = new \Bramus\Router\Router();
        $directories = scandir(_MODULES_);
        foreach($directories as $directory){
            $module = str_replace('directory/', '', $directory);
            if($module !== '.' && $module != '..'){
				$config = json_decode(file_get_contents(_MODULES_."/".$directory."/config.json"), true);
				if($config['active']){
                    $this->list[] = $config;
                	include_once(_MODULES_."/".$directory."/".$module.".php");
					$this->$module = new $module();
                    $this->router->mount("/".$module, function() use ($module){
                        $this->router->get("/(\w+)", function($fn) use ($module){
                            $fn = "action".$fn;
                            if(method_exists($module, $fn)){
                                echo json_encode($this->$module->$fn());
                                exit();
                            }
                        });
                    });
                    if(method_exists($module, "loadRoutes"))
                        $this->router = $this->$module->loadRoutes($this->router);
				}
            }
        }
    }
    function hooks($hook = false){
        $html = '';
        if($hook){
            foreach($this->list as $module){
                $module_name = $module['name'];
                $this->$module_name = new $module_name();
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
