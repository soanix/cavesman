<?php

namespace Cavesman;

class Config
{

    private static array $data = [];

    /**
     * @param string $config
     * @param $default
     * @return mixed
     */
    public static function get(string $config = '', $default = NULL): mixed
    {
        $params = explode(".", $config);

        if (isset(self::$data[$params[0]]))
            $config = self::$data[$params[0]];
        else {
            $file = Fs::APP_DIR . "/config/" . $params[0] . ".json";
            $config = [];
            if (!is_dir(dirname($file)))
                mkdir(dirname($file), 0777, true);

            if (file_exists($file)) {
                $config = json_decode(file_get_contents($file), true);
            }

            // Env config
            $file = Fs::APP_DIR . "/config/" . $params[0] . "." . self::getEnv() . ".json";

            if (file_exists($file)) {
                $config = array_replace_recursive($config, json_decode(file_get_contents($file), true));
            }

            self::$data[$params[0]] = $config;
        }

        $array = $config;

        if ($config) {

            foreach ($params as $key => $param) {

                if ($key) {
                    if (isset($array[$param])) {
                        $array = $array[$param];
                    } else {
                        return self::getValue($params, $default, $config);
                    }
                }
            }
        } else {
            return self::getValue($params, $default, $config);
        }

        return $array;
    }

    /**
     * Returns current environment
     *
     * @return string
     * @example dev, prod
     */
    public static function getEnv(): string
    {
        $file = Fs::APP_DIR . "/config/main.json";
        if (file_exists($file)) {
            $main = json_decode(file_get_contents($file), true);
        }
        if (isset($main['env']))
            return $main['env'];
        return 'dev';
    }

    /**
     * @param array $params
     * @param mixed $default
     * @param mixed $config
     * @return mixed
     */
    public static function getValue(array $params, mixed $default, mixed $config): mixed
    {
        $default_array = self::getDefaultArray($params, $default);
        $config = array_replace_recursive($config, $default_array);
        $fp = fopen(Fs::APP_DIR . "/config/" . $params[0] . ".json", "w+");
        fwrite($fp, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fclose($fp);
        return $default;
    }

    /**
     * Retrieve default config
     *
     * @param array $params
     * @param $value
     * @return array
     */
    private static function getDefaultArray(array $params = [], $value = null): array
    {
        $str = '';
        foreach ($params as $key => $param) {
            if ($key)
                $str .= '[' . $param . ']';
        }
        $arr = [];

        // Note: a different approach would be using explode() instead
        preg_match_all('/\[([^]]*)]/', $str, $has_keys, PREG_PATTERN_ORDER);

        if (isset($has_keys[1])) {

            $keys = $has_keys[1];
            $k = count($keys);
            if ($k > 1) {
                for ($i = 0; $i < $k - 1; $i++) {
                    $arr[$keys[$i]] = self::walk_keys($keys, $i + 1, $value);
                }
            } else {
                $arr[$keys[0]] = $value;
            }

            $arr = array_slice($arr, 0, 1);
        }

        return $arr;
    }

    /**
     * Walk through all array config keys
     *
     * @param $keys
     * @param $i
     * @param $value
     * @return array
     */
    private static function walk_keys($keys, $i, $value): array
    {
        $a = [];
        if (isset($keys[$i + 1])) {
            $a[$keys[$i]] = self::walk_keys($keys, $i + 1, $value);
        } else {
            $a[$keys[$i]] = $value;
        }
        return $a;
    }
}
