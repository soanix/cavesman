<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Model\Base;

/**
 * Used when model requires ID
 */
abstract class Model extends Base
{
    public ?int $id = 0;
}
