<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Db\Doctrine\Interface\Entity;
use Cavesman\Model\Base;

/**
 * Used when model requires ID
 */
abstract class Model extends Base implements Entity
{
    public ?int $id = 0;
}
