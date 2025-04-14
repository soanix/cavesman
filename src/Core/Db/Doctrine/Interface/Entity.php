<?php

namespace Cavesman\Db\Doctrine\Interface;

use Cavesman\Db\Doctrine\Entity\Base;

interface Entity {
    public function model(string $modelClass): ?\Cavesman\Db\Doctrine\Model\Base;
}
