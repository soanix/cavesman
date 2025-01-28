<?php

namespace Cavesman\Twig;

use Cavesman\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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
