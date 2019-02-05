<?php

namespace Cavesman;


/**
 * Smarty Class
 *
 * initializes basic smarty settings and act as smarty object
 *
 * @final   Smarty
 * @category    Libraries
 * @author  Md. Ali Ahsan Rana
 * @link    http://codesamplez.com/
 */
use Cavesman\modules;



class Smarty extends \Smarty {
    /**
     * constructor
     */
    public  static $instance;

    function __construct(){
        parent::__construct();
        $this->template_dir = "";
        $this->config_dir = _CONFIG_."/";
        $this->compile_dir = _CACHE_."/views/smarty/compile/";
		$this->cache_dir = _CACHE_."/views/smarty/cache/";
		$this->caching = false;
		$this->force_compile = true;
		$this->compile_check = true;
		$this->debugging = false;
		$this->registerPlugin("function", "hook", '\Cavesman\Smarty::smartyHook');
		$this->registerPlugin("function", "file", '\Cavesman\Smarty::smartyFile');
		$this->registerPlugin("function", "css", '\Cavesman\Smarty::smartyCss');
		$this->registerPlugin("function", "img", '\Cavesman\Smarty::smartyImgUrl');
		$this->registerPlugin("function", "js", '\Cavesman\Smarty::smartyJs');
	}
	public static function __install(){
		if(!is_dir(_CACHE_."/views"))
			mkdir(_CACHE_."/views");
		if(!is_dir(_CACHE_."/views/smaty"))
			mkdir(_CACHE_."/views/smarty");
		if(!is_dir(_CACHE_."/views/smarty/compile"))
			mkdir(_CACHE_."/views/smarty/compile");
	}

    public static function smartyFile($params, $smarty){
    	$name = isset($params['name']) ? $params['name'] : '';
    	include_once(_CLASSES_."/modules.class.php");
    	$modules = new \Cavesman\modules();
    	$plugin_info = $modules->list[str_replace(".tpl", "", $name)];
    	if(file_exists($plugin_info['directory']."/".$name))
    		return $smarty->fetch($plugin_info['directory']."/".$name);
    	else
    		return $smarty->fetch($name);
    }
    public static function smartyHook($params, $smarty){
    	$name = isset($params['name']) ? $params['name'] : '';
    	return FrontEnd::getInstance("Cavesman\modules")->hooks($name);
    }
    public static function smartyCss($params, $smarty){
    	$file = isset($params['file']) ? $params['file'] : '';
    	if(strpos($file, "/") !== 0){
            if(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/css/".$file))
                $css = _TEMPLATES_."/"._THEME_NAME_."/css/".$file;
            elseif(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/assets/css/".$file))
                $css = _TEMPLATES_."/"._THEME_NAME_."/assets/css/".$file;
    		$time = filemtime(_APP_."/".$css);
    	}else{
    		$css = $file;
    		$time = filemtime(_APP_.$css);
    	}


    	if($file)
    		return '<link rel="stylesheet" type="text/css" href="'.$css.'?'.$time.'">';
    	return '';
    }
    public static function smartyJs($params, $smarty){

    	$file = isset($params['file']) ? $params['file'] : '';
        if(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/js/".$file))
            $js = _TEMPLATES_."/"._THEME_NAME_."/js/".$file;
        elseif(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/assets/js/".$file))
            $js = _TEMPLATES_."/"._THEME_NAME_."/assets/js/".$file;
    	if(strpos($file, "/") !== 0){
    		$time = filemtime(_APP_."/".$js);
    	}else{
    		$js = $file;
    		$time = filemtime(_APP_.$js);
    	}
    	if($file)
    		return '<script src="'.$js.'?'.$time.'"></script>';
    	return "";
    }

    public static function smartyImgUrl($params, $smarty){

    	$file = isset($params['file']) ? $params['file'] : '';
        if(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/img/".$file))
            $url = _TEMPLATES_."/"._THEME_NAME_."/img/".$file;
        elseif(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/images/".$file))
            $url = _TEMPLATES_."/"._THEME_NAME_."/images/".$file;
        elseif(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/assets/img/".$file))
            $url = _TEMPLATES_."/"._THEME_NAME_."/assets/img/".$file;
        elseif(file_exists(_APP_._TEMPLATES_."/"._THEME_NAME_."/assets/images/".$file))
            $url = _TEMPLATES_."/"._THEME_NAME_."/assets/images/".$file;

    	if(strpos($file, "/") !== 0){
    		$time = filemtime(_APP_."/".$url);
    	}else{
    		$url = $file;
    		$time = filemtime(_APP_.$url);
    	}
    	if($file)
    		return $url.'?'.$time;
    	return "";
    }
}
