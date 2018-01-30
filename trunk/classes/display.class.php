<?
class Display {
    function __construct(){
        $this->modules = new modules();

    }
    function init(){
        $this->smarty = new SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";
    }
    function p($value = '', $default = ''){
        return isset($_POST[$value]) ? $_POST[$value] : $default;
    }
    function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
    function startTheme(){
		self::init();
    }
    function theme(){
		$this->smarty->assign("base", _PATH_);
        $this->smarty->assign("css", _TEMPLATES_."/"._THEME_NAME_."/css");
        $this->smarty->assign("js", _TEMPLATES_."/"._THEME_NAME_."/js");
        $this->smarty->assign("img", _TEMPLATES_."/"._THEME_NAME_."/img");
        include_once(_THEMES_."/"._THEME_NAME_."/index.php");
    }
}
