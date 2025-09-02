<?php

namespace Cavesman\Twig;

use Cavesman\FileSystem;
use Cavesman\Translate;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TranslateExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('trans', [$this, 'getTranslate']),
        ];
    }

    public function getTranslate($string, array $replace = []): string
    {
        return Translate::get($string, $replace);
    }
}
