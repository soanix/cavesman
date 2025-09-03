<?php

namespace Cavesman\Test\Model;

use Cavesman\Db\Doctrine\Model\Base;
use Cavesman\Test\Entity\Entity;
use DateTime;

class Model extends ModelBase
{
    /**
     * @var Model[] $children
     */
    public array $children = [];

}
