<?php

namespace Cavesman\Model;

use Cavesman\Config;
use DateTime;
use DateTimeZone;


class Time extends DateTime
{
    public function __construct(
        string $datetime = 'now',
        DateTimeZone|null $timezone = null
    ) {
        parent::__construct('0000-00-00T' . $datetime, $timezone);
    }
    public function toString(?string $format): string
    {
        $format = $format ?? Config::get('params.core.time.format', 'H:i');
        return $this->format($format);
    }
}
