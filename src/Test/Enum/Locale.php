<?php

namespace Cavesman\Test\Enum;

enum Locale: string implements \Cavesman\Interface\Locale {
    case en = 'en';
    case es = 'es';
}