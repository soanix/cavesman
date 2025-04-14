<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Db\Doctrine\Interface\Model;
use Cavesman\Model\Base as BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use ReflectionException;

/**
 * Used when model requires ID
 */
abstract class Base extends BaseModel implements Model
{

    /**
     * @throws ReflectionException
     */
    public function entity(bool $update = false): \Cavesman\Db\Doctrine\Entity\Base
    {
        $className = static::ENTITY;

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
                        $items[] = method_exists($item, 'entity') ? $item->entity() : $item;
                    }
                    $entity->{$propName} = new ArrayCollection($items);
                } elseif ($value instanceof Base) {
                    $entity->{$propName} = method_exists($value, 'entity') ? $value->entity() : $value;
                } else {
                    $entity->{$propName} = $value;
                }
            }
        }

        return $entity;
    }

    public function typeOfCollection(string $property): string
    {
        return match ($property) {
            default => throw new \RuntimeException('No model mapping for ' . $property),
        };
    }
}
