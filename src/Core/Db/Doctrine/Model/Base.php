<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Db;
use Cavesman\Db\Doctrine\Interface\Model;
use Cavesman\Exception\ModuleException;
use Cavesman\Model\Base as BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use ReflectionClass;
use ReflectionException;

/**
 * Used when model requires ID
 */
abstract class Base extends BaseModel implements Model
{

    /**
     * @param EntityManager|null $em
     * @return Db\Doctrine\Entity\Base
     * @throws ORMException
     * @throws ReflectionException
     * @throws ModuleException
     */
    public function entity(?EntityManager $em = null): Db\Doctrine\Entity\Base
    {
        $className = static::ENTITY;

        if(!$em)
            $em = Db::getManager();

        $entity = null;
        if($em && $this->id)
            $entity = $em->getReference($className, $this->id);

        if (!$entity)
            $entity = new $className();


        $modelReflection = new ReflectionClass($this);
        $entityReflection = new ReflectionClass($entity);

        foreach ($entityReflection->getProperties() as $entityProp) {
            $propName = $entityProp->getName();

            if ($modelReflection->hasProperty($propName)) {
                $modelProp = $modelReflection->getProperty($propName);
                $modelProp->setAccessible(true);
                $value = $modelProp->getValue($this);

                if (is_array($value)) {
                    $items = [];
                    foreach ($value as $item) {
                        if (!is_array($item))
                            $items[] = method_exists($item, 'entity') ? $item->entity($em) : $item;
                    }
                    $entity->{$propName} = new ArrayCollection($items);
                } elseif ($value instanceof Base) {
                    $entity->{$propName} = method_exists($value, 'entity') ? $value->entity($em) : $value;
                } else {
                    $entity->{$propName} = $value;
                }
            }
        }

        return $entity;
    }

    public function typeOfCollection(string $property): ?string
    {
        return null;
    }
}
