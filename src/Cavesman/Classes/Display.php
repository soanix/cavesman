<?php

namespace Cavesman;

use mysql_xdevapi\Exception;

class Display extends Cavesman
{
    public static $instance;

    public static function trans(string $string = '', array $binds = [], string $module = '', $iso = null): string
    {
        if (class_exists('\App\Modules\Lang\Lang')) {
            return \App\Modules\Lang\Lang::l($string, $binds, $module, $iso);
        } else {
            $binded = $string;
            foreach ($binds as $key => $value) {
                $binded = str_replace($key, $value, $binded);
            }
            return $binded;
        }
    }

    /**
     * Start theme operations
     */
    public static function startTheme(): void
    {

        self::init();
    }

    /**
     * Start theme operations
     */
    public static function startCli(): void
    {

        self::initCli();
    }

    /**
     * Init function to load Smarty
     */
    private static function init(): void
    {
        parent::__install();

        if (class_exists('\Smarty')) {
            $tmpl = _THEMES_ . "/" . _THEME_NAME_ . "/tpl";
            $smarty = self::getInstance(Smarty::class);
            $smarty->template_dir = $tmpl;
            $smarty->assign("template", $tmpl);
        }
        Modules::loadModules();

    }
    private static function initCli(): void
    {
        if (file_exists(_APP_ . "/routes.php"))
            include_once(_APP_ . "/routes.php");

        if (is_dir(_SRC_ . "/Routes"))
            foreach (glob(_SRC_ . "/Routes/*.php") as $routeFile)
                require_once $routeFile;

        parent::__install();
        Modules::loadModules();
    }

    /**
     * Load smarty base vars and start gui
     */
    public static function theme(): void
    {
        if (class_exists('\Smarty')) {
            $smarty = self::getInstance(Smarty::class);
            if (defined("_PATH_"))
                $smarty->assign("base", _PATH_);
            $smarty->assign("css", _TEMPLATES_ . "/" . _THEME_NAME_ . "/css");
            $smarty->assign("data", _ROOT_ . "/../data");
            $smarty->assign("root", _ROOT_);
            $smarty->assign("js", _TEMPLATES_ . "/" . _THEME_NAME_ . "/js");
            $smarty->assign("img", _TEMPLATES_ . "/" . _THEME_NAME_ . "/img");
            $smarty->assign("template", _THEME_);
        }

        if (file_exists(_APP_ . "/routes.php"))
            include_once(_APP_ . "/routes.php");
        else
            throw new Exception("No se ha encontrado el archivo routes.php",  500);

        if (is_dir(_SRC_ . "/Routes"))
            foreach (glob(_SRC_ . "/Routes/*.php") as $routeFile)
                require_once $routeFile;


        if (file_exists(_THEMES_ . "/" . _THEME_NAME_ . "/index.php")) {
            include_once(_THEMES_ . "/" . _THEME_NAME_ . "/index.php");
        }
    }
}
