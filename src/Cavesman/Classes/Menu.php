<?php

namespace Cavesman;

class Menu {

    public static $items = array();

    public static function addItem(array $item) : void {
        if(isset($item['items']))
            self::$items[] = $item;
        else
            foreach($item as $i)
                self::$items[] = $i;
    }

    public static function render(array $params = array(), $smarty = null) : string {
        $template_dir = Cavesman::$smarty->template_dir[0];
        $file = isset($params['file']) ? $params['file'] : false;
        $menu = isset($params['name']) ? $params['name'] : 'main';
        if($file && file_exists($template_dir."/".$file)){
            $file = $template_dir."/".$file;
        }elseif(file_exists($file)){

        }elseif(file_exists($template_dir."/partial/menu/sidebar-item.tpl")){
            $file = $template_dir."/partial/menu/sidebar-item.tpl";
        }
        if(!file_exists($file))
            throw new \Exception("TEMPLATE FILE NOT DEFINED OR DEFAULT TEMPLATE NOT FOUND (".$file.")");
        usort(self::$items, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        $html = '';

        foreach(self::$items as $item){
            if(
                (!isset($item['menu']) && $menu == 'main') ||
                (isset($item['menu']) && $item['menu'] == $menu)
            ){
                $binds = ["items" => $item];

                foreach($item['items'] as $i){
                    if(isset($i['binds'])){
                        foreach($i['binds'] as $name => $value){
                            $binds[$name] = is_object($value) ? $value() : $value;
                        }
                    }

                }
                $html .= Cavesman::$smarty->fetch($file, $binds);
            }
        }
        return $html;
    }
}
