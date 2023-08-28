<?php

namespace Cavesman;

use src\Modules\Lang;
use src\Modules\User;

class Display extends Cavesman
{
    public static $instance;

    public static function trans(string $string = '', array $binds = [], string $module = ''): string
    {
        if (class_exists(Lang::class)) {
            return Lang::l($string, $binds, $module);
        } else {
            $binded = $string;
            foreach ($binds as $key => $value) {
                $binded = str_replace($key, $value, $binded);
            }
            return $binded;
        }
    }

    /**
     * Get POST value by key
     *
     * @param string $string Text string to search in POST key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public static function p($string = '', $default = '')
    {
        return isset($_POST[$string]) ? $_POST[$string] : $default;
    }

    /**
     * Get GET value by key
     *
     * @param string $string Text string to search in GET key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public static function g($value = '', $default = '')
    {
        return isset($_GET[$value]) ? $_GET[$value] : $default;
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
        $tmpl = _THEMES_ . "/" . _THEME_NAME_ . "/tpl";
        $smarty = self::getInstance(Smarty::class);
        $smarty->template_dir = $tmpl;
        $smarty->assign("template", $tmpl);
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
        $smarty = self::getInstance(Smarty::class);
        if (defined("_PATH_"))
            $smarty->assign("base", _PATH_);
        $smarty->assign("css", _TEMPLATES_ . "/" . _THEME_NAME_ . "/css");
        $smarty->assign("data", _ROOT_ . "/../data");
        $smarty->assign("root", _ROOT_);
        $smarty->assign("js", _TEMPLATES_ . "/" . _THEME_NAME_ . "/js");
        $smarty->assign("img", _TEMPLATES_ . "/" . _THEME_NAME_ . "/img");
        $smarty->assign("template", _THEME_);

        if (file_exists(_APP_ . "/routes.php"))
            include_once(_APP_ . "/routes.php");
        else
            Display::response("No se ha encontrado el archivo routes.php", "json", 500);
        
        if (is_dir(_SRC_ . "/Routes"))
            foreach (glob(_SRC_ . "/Routes/*.php") as $routeFile)
                require_once $routeFile;
        
        
        if (file_exists(_THEMES_ . "/" . _THEME_NAME_ . "/index.php")) {
            include_once(_THEMES_ . "/" . _THEME_NAME_ . "/index.php");
        }
    }

    /**
     * Return error
     *
     * $msg String / Array Text of error
     * $type String Text of error
     * $header int Text of error
     */
    static function response($msg = '', $type = 'json', $code = 200)
    {
        switch ($code) {
            case 100:
                $text = 'Continue';
                break;
            case 101:
                $text = 'Switching Protocols';
                break;
            case 200:
                $text = 'OK';
                break;
            case 201:
                $text = 'Created';
                break;
            case 202:
                $text = 'Accepted';
                break;
            case 203:
                $text = 'Non-Authoritative Information';
                break;
            case 204:
                $text = 'No Content';
                break;
            case 205:
                $text = 'Reset Content';
                break;
            case 206:
                $text = 'Partial Content';
                break;
            case 300:
                $text = 'Multiple Choices';
                break;
            case 301:
                $text = 'Moved Permanently';
                break;
            case 302:
                $text = 'Moved Temporarily';
                break;
            case 303:
                $text = 'See Other';
                break;
            case 304:
                $text = 'Not Modified';
                break;
            case 305:
                $text = 'Use Proxy';
                break;
            case 400:
                $text = 'Bad Request';
                break;
            case 401:
                $text = 'Unauthorized';
                break;
            case 402:
                $text = 'Payment Required';
                break;
            case 403:
                $text = 'Forbidden';
                break;
            case 404:
                $text = 'Not Found';
                break;
            case 405:
                $text = 'Method Not Allowed';
                break;
            case 406:
                $text = 'Not Acceptable';
                break;
            case 407:
                $text = 'Proxy Authentication Required';
                break;
            case 408:
                $text = 'Request Time-out';
                break;
            case 409:
                $text = 'Conflict';
                break;
            case 410:
                $text = 'Gone';
                break;
            case 411:
                $text = 'Length Required';
                break;
            case 412:
                $text = 'Precondition Failed';
                break;
            case 413:
                $text = 'Request Entity Too Large';
                break;
            case 414:
                $text = 'Request-URI Too Large';
                break;
            case 415:
                $text = 'Unsupported Media Type';
                break;
            case 500:
                $text = 'Internal Server Error';
                break;
            case 501:
                $text = 'Not Implemented';
                break;
            case 502:
                $text = 'Bad Gateway';
                break;
            case 503:
                $text = 'Service Unavailable';
                break;
            case 504:
                $text = 'Gateway Time-out';
                break;
            case 505:
                $text = 'HTTP Version not supported';
                break;
            default:
                exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }
        $msg = $msg ? $msg : $text;
        if (strtolower($type) == "json") {
            header('Content-Type: application/json; Charset=UTF-8');
            self::json($msg);

        } elseif (strtolower($type == 'xlsx')) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; Charset=UTF-8');
            echo $msg;
        } elseif (strtolower($type == 'css')) {
            header('Content-Type: text/css; Charset=UTF-8');
            echo $msg;
        } elseif (strtolower($type == 'js')) {
            header('Content-Type: application/javascript; Charset=UTF-8');
            echo $msg;
        } elseif (strtolower($type) == "html") {
            if (file_exists(_THEME_ . "/tpl/error/" . $code . ".tpl")) {
                header('X-PHP-Response-Code: ' . $code, true, $code);
                if ($_SERVER['REQUEST_METHOD'] == "GET")
                    self::getInstance(Smarty::class)->display("error/" . $code . ".tpl");
                if ($_SERVER['REQUEST_METHOD'] == "POST")
                    self::response(array("error" => $msg), "json", $code);
            } else {
                header('X-PHP-Response-Code: ' . $code, true, $code);
                echo $msg;
            }
        } else {
            header('X-PHP-Response-Code: ' . $code, true, $code);
            echo $msg;
        }
        exit();
    }

    private static function json($array, $param = NULL)
    {
        echo json_encode($array, $param);
    }

    public static function can($name = "general", $group_name = " general")
    {
        if (class_exists(user::class))
            return User::can($name, $group_name);
        else
            return true;
    }
}
