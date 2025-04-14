<?php

namespace Cavesman\Model;

use DateTime;
use DateTimeZone;


class Time extends DateTime
{
    public function __construct(
        string $datetime = 'now',
        DateTimeZone|null $timezone = null
    ) {
        parent::__construct($datetime, $timezone);
    }
    public function toString($seconds = false): string
    {
        return $this->format('H:i' . ($seconds ? ' :s' : ''));
    }
}
