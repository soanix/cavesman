<?php

namespace Cavesman\Interface;

interface JWT {
    public array $headers {
        get;
        set;
    }
    public array $payload {
        get;
        set;
    }
    public string $signature {
        get;
        set;
    }
}