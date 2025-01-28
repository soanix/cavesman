<?php

namespace Cavesman\Twig;

use App\Tool\Fs;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Cavesman\Config;

class PathExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'getConfig']),
        ];
    }

    public function getConfig(string $path)
    {
        return Fs::getAbsolutePath($path);
    }
}
