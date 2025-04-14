<?php

namespace Cavesman\Db\Doctrine\Interface;

use Cavesman\Db\Doctrine\Entity\Base;

interface Model {
    const Base|string ENTITY = '';

    public function entity(bool $update = false): ?Base;
}
