<?

class modules {
    public $list = array();
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
        $directories = scandir(_MODULES_);
        foreach($directories as $directory){
            $module = str_replace('directory/', '', $directory);
            if($module !== '.' && $module != '..'){
				$config = json_decode(file_get_contents(_MODULES_."/".$directory."/config.json"), true);
				if($config['active']){
                	include_once(_MODULES_."/".$directory."/".$module.".php");
					$this->$module = new $module();
				}
            }
        }
    }
	function p($value = '', $default = ''){
        return isset($_POST[$value]) ? $_POST[$value] : $default;
    }
    function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
}
?>
