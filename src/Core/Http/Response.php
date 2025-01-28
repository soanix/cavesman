<?php

namespace Cavesman\Http;

use JetBrains\PhpStorm\NoReturn;

class Response
{
    #[NoReturn]
    public function __construct(mixed $message, int $code = 200, string $contentType = 'text/html')
    {
        header('X-PHP-Response-Code: ' . $code, true, $code);
        header('Content-Type: ' . $contentType);
        echo $message;
        exit();
    }
}
