<?

class modules {
    public $list = array();
    function __construct(){
        $this->db = new db();
		$this->lang = new lang();
        $this->loadModules();
		if(!is_dir(_ROOT_."/img/m"))
            mkdir(_ROOT_."/img/m");
    }
    function loadSmarty(){
        $this->smarty = new SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";
    }
	function l($string, $binds = array()){
		$this->lang = new lang();
		return $this->lang->l($string, $binds);
	}
    function loadModules(){
        $this->loadSmarty();
        $directories = scandir(_MODULES_);
        foreach($directories as $directory){
            $module = str_replace('directory/', '', $directory);
            if($module !== '.' && $module != '..'){
                include_once(_MODULES_."/".$directory."/".$module.".php");
                $this->$module = new $module();
                $this->list[$module] = $this->$module->config;
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
