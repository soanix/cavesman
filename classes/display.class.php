<?php
/**
 * This class acts as an example on where to position a DocBlock.
 */
class Display {

    /**
     * Init function to load Smarty
     */
    function init(){
        $this->smarty = new SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";
        $this->router = new Router\Router(_PATH_);
    }

    /**
     * Get POST value by key
     *
     * @param string $string Text string to search in POST key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public function p($string = '', $default = ''){
        return isset($_POST[$string]) ? $_POST[$string] : $default;
    }

    /**
     * Get GET value by key
     *
     * @param string $string Text string to search in GET key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }

    /**
     * Start theme operations
     */
    public function startTheme(){
        self::init();
    }

    /**
     * Load smarty base vars and start gui
     */
    public function theme(){
        $this->smarty->assign("base", _PATH_);
        $this->smarty->assign("css", _TEMPLATES_."/"._THEME_NAME_."/css");
        $this->smarty->assign("js", _TEMPLATES_."/"._THEME_NAME_."/js");
        $this->smarty->assign("img", _TEMPLATES_."/"._THEME_NAME_."/img");
        include_once(_THEMES_."/"._THEME_NAME_."/index.php");
    }
}
?>
