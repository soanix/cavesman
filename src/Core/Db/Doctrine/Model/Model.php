<?php

namespace Cavesman\Db\Doctrine\Model;

/**
 * Used when model requires ID
 */
abstract class Model extends Base
{
    public ?int $id = 0;
}
