<?

$controller = $display->g("controller", "home");
$section = $display->g("section");
$action = $display->g("action");
if($access){
    $display->theme = _THEME_NAME_;
}else
    $display->theme = "mantenimiento";

$display->startTheme();

if($controller == 'ajax' || $controller == 'page'){
	$action = "action".ucfirst($action);
    if(file_exists(_CONTROLLERS_."/".$section.".controller.php"))
        require_once _CONTROLLERS_."/".$section.".controller.php";
    $$section = new $section();
    if(file_exists(_CONTROLLERS_."/".$section.".controller.php") && method_exists($$section, $action)){
        $result = $$section->$action();
    }elseif(file_exists(_MODULES_."/".$section."/".$section.".php") && method_exists($display->modules->$section, $action)){
        $result = $display->modules->$section->$action();
    }

    echo $controller == "ajax" ? json_encode($result) : $result;

    exit();
}elseif(file_exists(_CONTROLLERS_."/".$controller.".controller.php")){

    require_once _CONTROLLERS_."/".$controller.".controller.php";
    if(class_exists($controller)){
        $$controller = new $controller();
        if(method_exists($$controller, $section)){
            $result = $$controller->$section();
            $display->smarty->assign($$controller->config['smartyList'], $result);
        }else{
			foreach(explode(",", $$controller->config['smartyList']) as $find){
				$$controller->find = $find;
	            $result = $$controller->init();
	            $display->smarty->assign($find, $result);
			}
        }
    }
}elseif(file_exists(_MODULES_."/".$controller."/".$controller.".php")){
    if($section){
        $result = $display->modules->$controller->single();
        $display->smarty->assign("single_info", $result);
    }
}elseif($controller && file_exists(_THEMES_."/static/tpl/".$controller.".tpl")){
    $display->smarty->template_dir = _THEMES_."/static/tpl/";
    require _THEMES_."/static/".$controller.".php";
    $display->smarty->display($controller.".tpl");
    exit();
}

$display->theme();
?>
