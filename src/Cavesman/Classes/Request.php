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
     * @return string
     */
    public static function post($string = '', $default = '')
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
    public static function get($value = '', $default = '')
    {
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
    
    /**
     * Get FILES value by key
     *
     * @param string $string Text string to search in GET key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public static function files($value = '', $default = null)
    {
        return isset($_FILES[$value]) ? $_FILES[$value] : $default;
    }
    
    /**
     * Get HEADER value by key
     *
     * @param string $string Text string to search in HEADER key
     * @param string $default Default value if key is not defined
     *
     * @return string
     */
    public static function header($key = '', $default = null) {
        $headers = apache_request_headers();

        if(!empty($headers[$key]))
            return $headers[$key];
        elseif (!empty($headers[mb_strtolower($key)]))
            return $headers[mb_strtolower($key)];
        return $default;
    }
    
}
