<?php
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

function smartyFile($params, $smarty){
	$name = isset($params['name']) ? $params['name'] : '';
	include_once(_CLASSES_."/modules.class.php");
	$modules = new \Cavesman\modules();
	$plugin_info = $modules->list[str_replace(".tpl", "", $name)];
	if(file_exists($plugin_info['directory']."/".$name))
		return $smarty->fetch($plugin_info['directory']."/".$name);
	else
		return $smarty->fetch($name);
}
function smartyHook($params, $smarty){
	$name = isset($params['name']) ? $params['name'] : '';
	$modules = new \Cavesman\modules();
	return $modules->hooks($name);
}
function smartyCss($params, $smarty){
	$file = isset($params['file']) ? $params['file'] : '';
	if(strpos($file, "/") !== 0){
		$css = _TEMPLATES_."/"._THEME_NAME_."/css/".$file;
		$time = filemtime(_APP_."/".$css);
	}else{
		$css = $file;
		$time = filemtime(_APP_.$css);
	}


	if($file)
		return '<link rel="stylesheet" type="text/css" href="'.$css.'?'.$time.'">';
	return '';
}
function smartyJs($params, $smarty){

	$file = isset($params['file']) ? $params['file'] : '';
	if(strpos($file, "/") !== 0){
		$js = _TEMPLATES_."/"._THEME_NAME_."/js/".$file;
		$time = filemtime(_APP_."/".$js);
	}else{
		$js = $file;
		$time = filemtime(_APP_.$js);
	}
	if($file)
		return '<script src="'.$js.'?'.$time.'"></script>';
	return "";
}
function smartyImgUrl($params, $smarty){

	$file = isset($params['file']) ? $params['file'] : '';
	if(strpos($file, "/") !== 0){
		$url = _TEMPLATES_."/"._THEME_NAME_."/img/".$file;
		$time = filemtime(_APP_."/".$url);
	}else{
		$url = $file;
		$time = filemtime(_APP_.$url);
	}
	if($file)
		return $url.'?'.$time;
	return "";
}

class SmartyCustom extends Smarty {
    /**
     * constructor
     */

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

		$this->registerPlugin("function", "hook", 'smartyHook');
		$this->registerPlugin("function", "file", 'smartyFile');
		$this->registerPlugin("function", "css", 'smartyCss');
		$this->registerPlugin("function", "img", 'smartyImgUrl');
		$this->registerPlugin("function", "js", 'smartyJs');
	}
	function __install(){
		if(!is_dir(_CACHE_."/views"))
			mkdir(_CACHE_."/views");
		if(!is_dir(_CACHE_."/views/smaty"))
			mkdir(_CACHE_."/views/smarty");
		if(!is_dir(_CACHE_."/views/smarty/compile"))
			mkdir(_CACHE_."/views/smarty/compile");
	}
}
