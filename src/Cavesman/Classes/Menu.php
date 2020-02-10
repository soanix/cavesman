<?php

namespace Cavesman;

class Menu {

    public static $items = array();

    public static function addItem(array $item) : void {
        self::$items[] = $item;
    }

    public static function render(array $params = array(), $smarty = null) : string {
        $template_dir = Cavesman::$smarty->template_dir[0];
        $file = isset($params['file']) ? $params['file'] : false;
        if($file && file_exists($template_dir."/".$file)){
            $file = $template_dir."/".$file;
        }elseif(file_exists($template_dir."/partial/menu/sidebar-item.tpl")){
            $file = $template_dir."/partial/menu/sidebar-item.tpl";
        }
        if(!$file)
            throw new \Exception("TEMPLATE FILE NOT DEFINED OR DEFAULT TEMPLATE NOT FOUND (/partial/menu/sidebar-item.tpl)");
        usort(self::$items, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        $html = '';
        foreach(self::$items as $item){
            $html .= Cavesman::$smarty->fetch($file, array("items" => $item));
        }
        return $html;
    }
}
