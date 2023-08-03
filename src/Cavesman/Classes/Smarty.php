<?php

namespace Cavesman;

use Cavesman\FrontEnd;
use lessc;
use ScssPhp\ScssPhp\Compiler;
use src\Modules\Lang;

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
class Smarty extends \Smarty
{
    /**
     * constructor
     */
    public static $instance;

    function __construct()
    {
        parent::__construct();
        $this->template_dir = "";
        $this->config_dir = _CONFIG_ . "/";
        $this->compile_dir = _CACHE_ . "/views/smarty/compile/";
        $this->cache_dir = _CACHE_ . "/views/smarty/cache/";
        $this->caching = Config::get("smarty.caching", false);
        $this->force_compile = Config::get("smarty.force_compile", true);
        $this->compile_check = Config::get("smarty.compile_check", true);
        $this->debugging = Config::get("smarty.debugging", false);
        $this->registerPlugin("function", "hook", '\Cavesman\Smarty::smartyHook');
        $this->registerPlugin("function", "file", '\Cavesman\Smarty::smartyFile');
        $this->registerPlugin("function", "css", '\Cavesman\Smarty::smartyCss');
        $this->registerPlugin("function", "img", '\Cavesman\Smarty::smartyImgUrl');
        $this->registerPlugin("function", "video", '\Cavesman\Smarty::smartyVideoUrl');
        $this->registerPlugin("function", "js", '\Cavesman\Smarty::smartyJs');
        $this->registerPlugin("function", "trans", '\Cavesman\Smarty::smartyLang');
        $this->registerPlugin("function", "can", '\Cavesman\Smarty::smartyCan');
        $this->registerPlugin("function", "menu", '\Cavesman\Menu::render');
        $this->registerPlugin("function", "git_version", '\Cavesman\Git::version');
        $this->registerPlugin("function", "config", '\Cavesman\Smarty::smartyConfig');

        $this->registerModifiers();

    }

    public static function getInstance(): static
    {
        if ((self::$instance instanceof self) === false) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function __install()
    {
        if (!is_dir(_CACHE_ . "/views"))
            mkdir(_CACHE_ . "/views");
        if (!is_dir(_CACHE_ . "/views/smaty"))
            mkdir(_CACHE_ . "/views/smarty");
        if (!is_dir(_CACHE_ . "/views/smarty/compile"))
            mkdir(_CACHE_ . "/views/smarty/compile");
    }

    public static function smartyCan($params, $smarty)
    {
        $name = isset($params['do']) ? $params['do'] : 'general';
        $group_name = isset($params['group']) ? $params['group'] : '';

        if (class_exists('\src\Modules\User') && method_exists('\src\Modules\User', 'can'))
            return \src\Modules\User::can($name, $group_name);
        else
            return true;
    }

    public static function smartyLang($params, $smarty)
    {
        $s = isset($params['s']) ? $params['s'] : '';
        $r = isset($params['r']) ? $params['r'] : array();
        $m = isset($params['m']) ? $params['m'] : '';
        return Display::trans($s, $r, $m);
    }

    public static function smartyFile($params, $smarty): string
    {
        $name = isset($params['name']) ? $params['name'] : '';
        include_once(_CLASSES_ . "/modules.class.php");
        $modules = Cavesman::getInstance(Modules::class);
        $plugin_info = $modules->list[str_replace(".tpl", "", $name)];
        if (file_exists($plugin_info['directory'] . "/" . $name))
            return self::partial($plugin_info['directory'] . "/" . $name);
        else
            return self::partial($name);
    }

    public static function smartyHook($params, $smarty)
    {
        foreach ($params as $key => $param) {
            self::set($key, $param);
        }
        return Cavesman::getInstance(Modules::class)->hooks($params['name']);
    }

    public static function smartyCss($params, $smarty)
    {
        $file = isset($params['file']) ? $params['file'] : '';
        $template = isset($params['template']) ? $params['template'] : false;
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!is_dir(_WEB_ . "/c"))
            mkdir(_WEB_ . "/c");
        if (!is_dir(_WEB_ . "/c/css"))
            mkdir(_WEB_ . "/c/css");

        $file = isset($params['file']) ? $params['file'] : '';

        if (file_exists($file)) {
            $name = hash("sha256", $file . "-" . filemtime($file));
            $new_file = _WEB_ . "/c/css/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            $css = _PATH_ . "c/css/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            if (!file_exists($new_file)) {
                if ($extension == 'less') {
                    $less = new lessc;
                    $compiled = $less->compileFile($css);
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } else {
                    copy($file, $new_file);
                }
            }
            $time = "";
        } elseif (stripos($file, "/") !== 0 && stripos($file, "://") === false) {
            if (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/css/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/css/" . $file;
            elseif (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/css/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/css/" . $file;

            $name = hash("sha256", $file . "-" . filemtime($f));
            $new_file = _WEB_ . "/c/css/" . $name . ".css";
            $css = _PATH_ . "c/css/" . $name . ".css";
            if (!file_exists($new_file)) {
                if ($extension == 'less') {
                    $less = new lessc;
                    $compiled = $less->compileFile($f);
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } elseif ($extension == 'scss') {
                    $scss = new Compiler();
                    $scss->setImportPaths(dirname($f));
                    $compiled = $scss->compile('@import "' . basename($f) . '";');
                    $fp = fopen($new_file, "w+");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } elseif ($template) {
                    $compiled = self::partial($f);

                    $fp = fopen($new_file, "w+");
                    fwrite($fp, "/* File: " . $file . "*/\n\n");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } else {
                    copy($f, $new_file);
                }
            }
            $time = "";

        } else {
            $css = $file;
            $time = false;
        }

        if ($file)
            return '<link rel="stylesheet" type="text/css" href="' . $css . ($time ? '?' . $time : '') . '">';
        return '';
    }

    public static function smartyJs($params, $smarty)
    {
        $file = isset($params['file']) ? $params['file'] : '';
        $template = isset($params['template']) ? $params['template'] : false;
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (!is_dir(_WEB_ . "/c"))
            mkdir(_WEB_ . "/c");
        if (!is_dir(_WEB_ . "/c/js"))
            mkdir(_WEB_ . "/c/js");


        if (file_exists($file)) {
            $name = hash("sha256", $file . "-" . Lang::$iso . "-" . filemtime($file));
            $new_file = _WEB_ . "/c/js/" . $name . ".js";
            $js = _PATH_ . "c/js/" . $name . ".js";
            if (!file_exists($new_file)) {
                if ($template) {
                    $compiled = self::partial($file);

                    $fp = fopen($new_file, "w+");
                    fwrite($fp, "/* File: " . $file . "*/\n\n");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } else {
                    copy($file, $new_file);
                }
            }
            $time = "";
        } elseif (stripos($file, "/") !== 0 && stripos($file, "://") === false) {
            if (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/js/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/js/" . $file;
            elseif (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/js/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/js/" . $file;
            $name = hash("sha256", $file . "-" . filemtime($f));
            $new_file = _WEB_ . "/c/js/" . $name . ".js";
            $js = _PATH_ . "c/js/" . $name . ".js";
            if (!file_exists($new_file)) {
                if ($template) {
                    $compiled = self::partial($f);

                    $fp = fopen($new_file, "w+");
                    fwrite($fp, "/* File: " . $file . "*/\n\n");
                    fwrite($fp, $compiled);
                    fclose($fp);
                } else {
                    copy($f, $new_file);
                }

            }
            $time = "";
        } else {
            $js = $file;
            $time = false;
        }
        if ($file)
            return '<script src="' . $js . ($time ? '?' . $time : '') . '"></script>';
        return "";
    }

    public static function smartyImgUrl($params, $smarty)
    {

        $file = isset($params['file']) ? $params['file'] : '';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!is_dir(_WEB_ . "/c"))
            mkdir(_WEB_ . "/c");
        if (!is_dir(_WEB_ . "/c/img"))
            mkdir(_WEB_ . "/c/img");

        if (file_exists($file)) {
            $name = hash("sha256", $file . "-" . filemtime($file));
            $new_file = _WEB_ . "/c/img/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            $img = _PATH_ . "c/img/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            if (!file_exists($new_file)) {
                copy($file, $new_file);
            }
            $time = "";
        } elseif (stripos($file, "/") !== 0 && stripos($file, "://") === false) {
            if (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/img/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/img/" . $file;
            elseif (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/img/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/img/" . $file;

            if ($f && file_exists($f)) {
                $name = hash("sha256", $file . "-" . @filemtime($f));
                $new_file = _WEB_ . "/c/img/" . $name . "." . $extension;
                $img = _PATH_ . "c/img/" . $name . "." . $extension;
                if (!file_exists($new_file)) {
                    copy($f, $new_file);
                }
                $time = "";
            }

        } else {
            $img = $file;
            $time = false;
        }

        if ($file && !empty($img))
            return $img . '?' . $time;
        return "";
    }

    public static function smartyVideoUrl($params, $smarty)
    {

        $file = isset($params['file']) ? $params['file'] : '';
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!is_dir(_WEB_ . "/c"))
            mkdir(_WEB_ . "/c");
        if (!is_dir(_WEB_ . "/c/video"))
            mkdir(_WEB_ . "/c/video");

        if (file_exists($file)) {

            $name = hash("sha256", $file . "-" . filemtime($file));
            $new_file = _WEB_ . "/c/video/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            $video = _PATH_ . "c/video/" . $name . "." . pathinfo($file, PATHINFO_EXTENSION);
            if (!file_exists($new_file)) {
                copy($file, $new_file);
            }
            $time = "";
        } elseif (stripos($file, "/") !== 0 && stripos($file, "://") === false) {
            if (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/video/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/video/" . $file;
            elseif (file_exists(_SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/video/" . $file))
                $f = _SRC_ . _TEMPLATES_ . "/" . _THEME_NAME_ . "/assets/video/" . $file;

            if (file_exists($file) && !empty($name)) {
                $name = hash("sha256", $file . "-" . @filemtime($f));
                $new_file = _WEB_ . "/c/video/" . $name . "." . $extension;
                $video = _PATH_ . "c/video/" . $name . "." . $extension;
                if (!file_exists($new_file)) {
                    copy($f, $new_file);
                }
                $time = "";
            }

        } else {
            $video = $file;
            $time = false;
        }

        if ($file && !empty($video))
            return $video . '?' . $time;
        return "";
    }

    public static function smartyConfig($params, $smarty)
    {
        $name = isset($params['get']) ? $params['get'] : '';
        return Config::get($name);

    }

    public static function partial($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        return self::getInstance()->fetch($template, $cache_id, $compile_id, $parent); // TODO: Change the autogenerated stub
    }

    public static function set($tpl_var, $value = null, $nocache = false)
    {
        return self::getInstance()->assign($tpl_var, $value, $nocache); // TODO: Change the autogenerated stub
    }

    /**
     * @return void
     * @throws \SmartyException
     */
    private function registerModifiers()
    {
        // registerPlugin para el modificador 'count'
        $this->registerPlugin('modifier', 'count', 'count');

        // registerPlugin para el modificador 'capitalize'
        $this->registerPlugin('modifier', 'capitalize', 'ucfirst');

        // registerPlugin para el modificador 'count_characters'
        $this->registerPlugin('modifier', 'count_characters', function ($string) {
            return mb_strlen($string, 'UTF-8');
        });

        // registerPlugin para el modificador 'count_paragraphs'
        $this->registerPlugin('modifier', 'count_paragraphs', function ($string) {
            return count(preg_split('/\n\s*\n/', $string, -1, PREG_SPLIT_NO_EMPTY));
        });

        // registerPlugin para el modificador 'count_sentences'
        $this->registerPlugin('modifier', 'count_sentences', function ($string) {
            return count(preg_split('/(?<=[.!?])\s+/', $string, -1, PREG_SPLIT_NO_EMPTY));
        });

        // registerPlugin para el modificador 'count_words'
        $this->registerPlugin('modifier', 'count_words', function ($string) {
            return str_word_count($string);
        });


        // registerPlugin para el modificador 'escape'
        $this->registerPlugin('modifier', 'escape', 'htmlspecialchars');

        // registerPlugin para el modificador 'indent'
        $this->registerPlugin('modifier', 'indent', function ($input, $count = 4, $indentChar = ' ') {
            return preg_replace('/^/m', str_repeat($indentChar, $count), $input);
        });

        // registerPlugin para el modificador 'intval'
        $this->registerPlugin('modifier', 'ceil', 'ceil');

        // registerPlugin para el modificador 'intval'
        $this->registerPlugin('modifier', 'intval', 'intval');

        // registerPlugin para el modificador 'lower'
        $this->registerPlugin('modifier', 'lower', 'strtolower');

        // registerPlugin para el modificador 'nl2br'
        $this->registerPlugin('modifier', 'nl2br', 'nl2br');

        // registerPlugin para el modificador 'regex_replace'
        $this->registerPlugin('modifier', 'regex_replace', function ($input, $pattern, $replace) {
            return preg_replace($pattern, $replace, $input);
        });

        // registerPlugin para el modificador 'replace'
        $this->registerPlugin('modifier', 'replace', function ($input, $search, $replace) {
            return str_replace($search, $replace, $input);
        });

        // registerPlugin para el modificador 'reset'
        $this->registerPlugin('modifier', 'reset', function ($array) {
            if (is_array($array)) {
                reset($array);
                return current($array);
            } else {
                return $array;
            }
        });

        // registerPlugin para el modificador 'spacify'
        $this->registerPlugin('modifier', 'spacify', function ($input, $spaceChar = ' ') {
            return implode($spaceChar, str_split($input));
        });

        // registerPlugin para el modificador 'string_format'
        $this->registerPlugin('modifier', 'string_format', 'sprintf');

        // registerPlugin para el modificador 'string_format'
        $this->registerPlugin('modifier', 'trim', 'trim');

        // registerPlugin para el modificador 'strip'
        $this->registerPlugin('modifier', 'strip', 'trim');

        // registerPlugin para el modificador 'strip_tags'
        $this->registerPlugin('modifier', 'strip_tags', 'strip_tags');

        // registerPlugin para el modificador 'substr'
        $this->registerPlugin('modifier', 'substr', function ($string, $start, $length = null) {
            if ($length !== null) {
                return mb_substr($string, $start, $length, 'UTF-8');
            } else {
                return mb_substr($string, $start, null, 'UTF-8');
            }
        });

        // registerPlugin para el modificador 'str_pad'
        $this->registerPlugin('modifier', 'str_pad', function ($string, $length, $padString = ' ', $padType = STR_PAD_RIGHT) {
            return str_pad($string, $length, $padString, $padType);
        });

        // registerPlugin para el modificador 'upper'
        $this->registerPlugin('modifier', 'upper', 'strtoupper');

        // registerPlugin para el modificador 'in_array'
        $this->registerPlugin('modifier', 'in_array', function ($value, $array) {
            return in_array($value, $array);
        });

    }
}
