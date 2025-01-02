<?php

namespace Cavesman\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Cavesman\Config;

class ConfigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this, 'getConfig']),
        ];
    }

    public function getConfig(string $key, $default = null)
    {
        return Config::get($key, $default);
    }
}
