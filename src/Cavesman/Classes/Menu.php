<?php

namespace Cavesman;

use Exception;

class Menu
{
    public static $items = [];

    public static function addItem(array $item): void
    {
        if (self::isAssoc($item)) {
            $name = $item['name'] ?? "main";
            self::processMenu($name, $item);
        } else {
            foreach ($item as $itm) {
                $name = $itm['name'] ?? "main";
                self::processMenu($name, $itm);
            }
        }
    }

    private static function processMenu($name, $menu): void
    {
        if (!isset(self::$items[$name])) {
            self::$items[$name] = [
                "name" => $name,
                "items" => []
            ];
        }

        if (isset($menu['items']) && $menu['items']) {
            foreach ($menu['items'] as $menuItem) {
                if (!isset(self::$items[$name]['items'][$menuItem['name']])) {
                    $isVisible = self::isMenuItemVisible($name, $menuItem);
                    if ($isVisible) {
                        self::$items[$name]['items'][$menuItem['name']] = $menuItem;
                        if (isset($menuItem['childs']) && $menuItem['childs']) {
                            self::$items[$name]['items'][$menuItem['name']]['childs']['items'] = array_filter(
                                $menuItem['childs']['items'],
                                fn($child) => self::isMenuItemVisible($name, $child)
                            );
                        }
                    }
                }
            }
        }
    }

    private static function isMenuItemVisible($menuName, $menuItem)
    {
        $configKey = "menu.{$menuName}.items.{$menuItem['name']}.visible";
        return Config::get($configKey, true) && ($menuItem['visible'] ?? true);
    }

    private static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function render(array $params = array(), $smarty = null): string
    {
        $template_dir = Smarty::getInstance()->template_dir[0];
        $file = isset($params['file']) ? $params['file'] : false;
        $menu = isset($params['name']) ? $params['name'] : 'main';
        if ($file && file_exists($template_dir . "/" . $file)) {
            $file = $template_dir . "/" . $file;
        } elseif (file_exists($file)) {

        } elseif (file_exists($template_dir . "/partial/menu/sidebar-item.tpl")) {
            $file = $template_dir . "/partial/menu/sidebar-item.tpl";
        }
        if (!file_exists($file))
            throw new Exception("TEMPLATE FILE NOT DEFINED OR DEFAULT TEMPLATE NOT FOUND (" . $file . ")");

        $html = '';

        foreach (self::$items as $item) {
            if (
                (!isset($item['name']) && $menu == 'main') ||
                (isset($item['name']) && $item['name'] == $menu)
            ) {

                $binds = ["items" => $item];

                foreach ($item['items'] as $i) {
                    if (isset($i['binds'])) {
                        foreach ($i['binds'] as $name => $value) {
                            $binds[$name] = is_object($value) ? $value() : $value;
                        }
                    }

                }
                usort($binds['items']['items'], function ($a, $b) {
                    return $a['order'] <=> $b['order'];
                });
                $html .= Smarty::partial($file, $binds);
            }
        }
        return $html;
    }
}
