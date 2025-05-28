<?php

namespace Cavesman\Db\Doctrine\Interface;

use Cavesman\Db\Doctrine\Entity\Base;
use Doctrine\ORM\EntityManager;

interface Model {
    const Base|string ENTITY = '';

    public function entity(?EntityManager $em = null): ?Base;
}
