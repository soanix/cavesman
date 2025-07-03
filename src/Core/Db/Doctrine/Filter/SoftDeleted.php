<?php

namespace Cavesman\Db\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleted extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->hasField('deletedOn')) {
            return '';
        }

        $columnName = $targetEntity->getColumnName('deletedOn');
        return sprintf('%s.%s IS NULL', $targetTableAlias, $columnName);
    }
}