<?php

namespace Cavesman\Enum\Console;

enum Type
{
    case ERROR;
    case WARNING;
    case SUCCESS;
    case INFO;
    case PROGRESS;
}
