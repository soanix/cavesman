<?php

namespace Cavesman;

class Menu {

    public static $items;

    public static function addItem($item) {
        self::$items[] = $item;
    }

    public static function render($params, $smarty) {
        $file = isset($params['file']) ? $params['file'] : false;
        if($file && file_exists(self::$smarty->template_dir."/".$file)){
            $file = self::$smarty->template_dir."/".$file;
        }elseif(file_exists(self::$smarty->template_dir."/partial/menu/sidebar-item.tpl")){
            $file = self::$smarty->template_dir."/partial/menu/sidebar-item.tpl";
        }
        usort(self::$items, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        $html = '';
        foreach(self::$items as $item){
            $html .= self::$smarty->fetch($file, array("items" => $item));
        }
        return $html;
    }
}
