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
include_once(_ROOT_."/externals/smarty/libs/Smarty.class.php");
function smartyMoneyFormat($params, &$smarty){
	include_once(_CLASSES_."/display.class.php");
	$display = new display();
	$n = isset($params['n']) ? $params['n'] : 0;
	$d = isset($params['d']) ? $params['d'] : 2;
	return $display->format->money($n, $d);
}
function smartyTranslate($params, &$smarty){
	$lang = new lang();
	$s = isset($params['s']) ? $params['s'] : '';
	$r = isset($params['r']) ? $params['r'] : array();
	return $s ? $lang->l($s, $r) : '';
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
		$this->caching = 0;
		$this->force_compile = false;
		$this->compile_check = true;
		$this->debugging = false;
		$this->registerPlugin("function", "l", 'smartyTranslate'); // Use Smarty 3 API calls, only if PHP version > 5.1.2
		$this->registerPlugin("function", "money", 'smartyMoneyFormat'); // Use Smarty 3 API calls, only if PHP version > 5.1.2
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
