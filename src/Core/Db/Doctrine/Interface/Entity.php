<?php

namespace Cavesman\Db\Doctrine\Interface;

use Cavesman\Db\Doctrine\Entity\Base;

interface Entity {
    public function entity(Base|string $entityClass, bool $update = false): ?Base;
}
