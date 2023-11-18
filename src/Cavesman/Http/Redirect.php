<?php

namespace Cavesman\Http;

use Cavesman\Http;

class Redirect {
    public function __construct(string $url, int $code = 200)
    {
        header('Location: ' . $url, true, $code);
        die();
    }
}
