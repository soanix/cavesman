<?php

namespace Cavesman\Twig;

use Cavesman\FileSystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PathExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'getPath']),
        ];
    }

    public function getPath(string $path)
    {
        return FileSystem::getPath($path);
    }
}
