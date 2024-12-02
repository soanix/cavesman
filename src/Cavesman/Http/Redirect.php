<?php

namespace Cavesman\Http;

use Cavesman\Http;
use JetBrains\PhpStorm\NoReturn;

class Redirect {
    #[NoReturn]
    public function __construct(string $url, int $code = 200)
    {
        header('Location: ' . $url, true, $code);
        die();
    }
}
