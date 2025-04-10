<?php

namespace Cavesman\Test\Model;
use Cavesman\Db\Doctrine\Model\Base;
use Doctrine\Common\Collections\ArrayCollection;

class Model extends Base
{
    public string $name = '';

    /**
     * @var Model[]
     */
    public array $children = [];

    public function entity(\Cavesman\Db\Doctrine\Entity\Base|string $entityClass, bool $update = false): ?\Cavesman\Db\Doctrine\Entity\Base
    {
        $entity = parent::entity($entityClass, $update);
        $entity->children = new ArrayCollection(
            array_map(fn(Base $child) => $child->entity(get_class($child)), $this->children)
        );

        return $entity;
    }
}
