<?php

namespace Cavesman\Http;

use JetBrains\PhpStorm\NoReturn;

class JsonResponse {
    #[NoReturn]
    public function __construct(mixed $message, int $code = 200, $flags = null)
    {
        header('X-PHP-Response-Code: ' . $code, true, $code);
        header('Content-Type: application/json');
        echo json_encode($message, $flags ?: JSON_PRETTY_PRINT);
        exit();
    }
}
