<?php

namespace Cavesman;
use ScssPhp\ScssPhp\Compiler;

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
use Cavesman\FrontEnd;
use Cavesman\Modules;

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
        $this->registerPlugin("function", "l", '\Cavesman\Smarty::smartyLang');
        $this->registerPlugin("function", "can", '\Cavesman\Smarty::smartyCan');
        $this->registerPlugin("function", "menu", '\Cavesman\Menu::render');
        $this->registerPlugin("function", "git_version", '\Cavesman\Git::version');
	}
	public static function __install(){
		if(!is_dir(_CACHE_."/views"))
			mkdir(_CACHE_."/views");
		if(!is_dir(_CACHE_."/views/smaty"))
			mkdir(_CACHE_."/views/smarty");
		if(!is_dir(_CACHE_."/views/smarty/compile"))
			mkdir(_CACHE_."/views/smarty/compile");
	}
    public static function smartyCan($params, $smarty){
        $name = isset($params['do']) ? $params['do'] : 'general';
        $group_name = isset($params['group']) ? $params['group'] : '';

        if(class_exists(\src\Modules\user::class))
            return \src\Modules\User::can($name, $group_name);
        else
            return true;
    }
    public static function smartyLang($params, $smarty){
        $s = isset($params['s']) ? $params['s'] : '';
    	$r = isset($params['r']) ? $params['r'] : array();
        $m = isset($params['m']) ? $params['m'] : '';
        return Display::trans($s, $r, $m);
    }
    public static function smartyFile($params, $smarty) : string {
    	$name = isset($params['name']) ? $params['name'] : '';
    	include_once(_CLASSES_."/modules.class.php");
    	$modules = FrontEnd::getInstance(Modules::class);
    	$plugin_info = $modules->list[str_replace(".tpl", "", $name)];
    	if(file_exists($plugin_info['directory']."/".$name))
    		return $smarty->fetch($plugin_info['directory']."/".$name);
    	else
    		return $smarty->fetch($name);
    }
    public static function smartyHook($params, $smarty){
    	foreach($params as $key => $param){
            Cavesman::$smarty->assign($key, $param);
        }
    	return Cavesman::getInstance(Modules::class)->hooks($params['name']);
    }

    public static function smartyCss($params, $smarty){
    	$file = isset($params['file']) ? $params['file'] : '';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if(!is_dir(_WEB_."/c"))
            mkdir(_WEB_."/c");
        if(!is_dir(_WEB_."/c/css"))
            mkdir(_WEB_."/c/css");

    	$file = isset($params['file']) ? $params['file'] : '';

        if(file_exists($file)){
            $name = hash("sha256", $file."-".filemtime($file));
            $new_file = _WEB_."/c/css/".$name.".".pathinfo($file, PATHINFO_EXTENSION);
            $css = _PATH_."c/css/".$name.".".pathinfo($file, PATHINFO_EXTENSION);
            if(!file_exists($new_file)){
                if($extension == 'less'){
                    $less = new \lessc;
                    $compiled = $less->compileFile($css);
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                }else{
                    copy($file, $new_file);
                }
            }
            $time = "";
    	}elseif(stripos($file, "/") !== 0 && stripos($file, "://") === false ){
            if(file_exists(_SRC_._TEMPLATES_."/"._THEME_NAME_."/css/".$file))
                $f = _SRC_._TEMPLATES_."/"._THEME_NAME_."/css/".$file;
            elseif(file_exists(_SRC_._TEMPLATES_."/"._THEME_NAME_."/assets/css/".$file))
                $f = _SRC_._TEMPLATES_."/"._THEME_NAME_."/assets/css/".$file;
            $name = hash("sha256", $file."-".filemtime($f));
            $new_file = _WEB_."/c/css/".$name.".css";
            $css = _PATH_."c/css/".$name.".css";
            if(!file_exists($new_file)){
                if($extension == 'less'){
                    $less = new \lessc;
                    $compiled = $less->compileFile($f);
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                }elseif($extension == 'scss'){
                    $scss = new Compiler();
                    $scss->setImportPaths(dirname($f));
                    $compiled = $scss->compile('@import "'.basename($f).'";');
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                }else{
                    copy($f, $new_file);
                }
            }
    		$time = "";

    	}else{
    		$css = $file;
    		$time = false;
    	}

        if($file)
    		return '<link rel="stylesheet" type="text/css" href="'.$css.($time ? '?'.$time : '').'">';
    	return '';
    }
    public static function smartyJs($params, $smarty){
        if(!is_dir(_WEB_."/c"))
            mkdir(_WEB_."/c");
        if(!is_dir(_WEB_."/c/js"))
            mkdir(_WEB_."/c/js");
    	$file = isset($params['file']) ? $params['file'] : '';

        if(file_exists($file)){
            $name = hash("sha256", $file."-".filemtime($file));
            $new_file = _WEB_."/c/js/".$name.".js";
            $js = _PATH_."c/js/".$name.".js";
            if(!file_exists($new_file)){
                copy($file, $new_file);
            }
            $time = "";
    	}elseif(stripos($file, "/") !== 0 && stripos($file, "://") === false ){
            if(file_exists(_SRC_._TEMPLATES_."/"._THEME_NAME_."/js/".$file))
                $f = _SRC_._TEMPLATES_."/"._THEME_NAME_."/js/".$file;
            elseif(file_exists(_SRC_._TEMPLATES_."/"._THEME_NAME_."/assets/js/".$file))
                $f = _SRC_._TEMPLATES_."/"._THEME_NAME_."/assets/js/".$file;
            $name = hash("sha256", $file."-".filemtime($f));
            $new_file = _WEB_."/c/js/".$name.".js";
            $js = _PATH_."c/js/".$name.".js";
            if(!file_exists($new_file)){
                copy($f, $new_file);
            }
    		$time = "";
    	}else{
    		$js = $file;
    		$time = false;
    	}
    	if($file)
    		return '<script src="'.$js.($time ? '?'.$time : '').'"></script>';
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
