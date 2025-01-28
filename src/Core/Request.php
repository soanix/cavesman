<?php

namespace Cavesman;

class Request
{

    /**
     * Get POST value by key
     *
     * @param string $string Text string to search in POST key
     * @param string $default Default value if key is not defined
     *
     * @return  string|array|null|mixed
     */
    public static function post($string = '', $default = '')
    {
        return $_POST[$string] ?? $default;
    }

    /**
     * Get GET value by key
     *
     * @param string $string Text string to search in GET key
     * @param string $default Default value if key is not defined
     *
     * @return string|array|null|mixed
     */
    public static function get($value = '', $default = '')
    {
        return $_GET[$value] ?? $default;
    }

    /**
     * Get FILES value by key
     *
     * @param string $string Text string to search in GET key
     * @param string|null $default Default value if key is not defined
     *
     * @return string|array|null|mixed
     */
    public static function files($value = '', ?string $default = null): mixed
    {
        return $_FILES[$value] ?? $default;
    }

    /**
     * Get HEADER value by key
     *
     * @param string $key
     * @param string|null $default Default value if key is not defined
     *
     * @return string|array|null|mixed
     */
    public static function header(string $key = '', ?string $default = null): mixed
    {
        $headers = apache_request_headers();

        if (!empty($headers[$key]))
            return $headers[$key];
        elseif (!empty($headers[mb_strtolower($key)]))
            return $headers[mb_strtolower($key)];
        return $default;
    }

    /**
     * Get current request domain
     * 
     * @return string|null
     */
    public static function getDomain(): ?string
    {
        /** RELATIVE PATHS**/
        if (PHP_SAPI !== 'cli') {
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            } else {
                if (isset($_SERVER['HTTPS'])) {
                    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
                } else {
                    $protocol = 'http';
                }
            }
            return $protocol . "://" . $_SERVER['HTTP_HOST'];
        }

        return null;
    }

}
