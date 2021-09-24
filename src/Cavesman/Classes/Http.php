<?php

namespace Cavesman;

class Http {

    public static function response($message, $code = 200, $contentType = 'html') {
        header('X-PHP-Response-Code: '.$code, true, $code);
        header('Content-Type: ' . $contentType);
        echo $message;
        exit();
    }

    public static function jsonResponse($message, $code = 200) {
        return self::response(json_encode($message), $code, 'application/json');
    }

}
