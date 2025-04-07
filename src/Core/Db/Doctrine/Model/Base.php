<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Db\Doctrine\Interface\Entity;
use Cavesman\Model\Base as BaseModel;

/**
 * Used when model requires ID
 */
abstract class Base extends BaseModel implements Entity
{
    public function entity(bool $update = false): ?\Cavesman\Db\Doctrine\Entity\Base
    {
        $modelClass = get_class($this); // Ej: App\Model\Enterprise
        $entityClass = str_replace('App\\Model', 'App\\Entity', $modelClass); // App\Entity\Enterprise

        if (!class_exists($entityClass)) {
            return null;
        }

        if ($this->id) {
            $entity = $entityClass::findOneBy(['id' => $this->id, 'deletedOn' => null]);
            if (!$entity)
                throw new \Exception('item.error.not-found', 404);

            if (!$update)
                return $entity;
        } else

        $entityInstance = new $entityClass();

        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            // Si es una instancia de Base del modelo, convertirla a entidad
            if ($value instanceof \Cavesman\Model\Base) {
                $value = $value->entity();
            }

            // Si es array o Traversable, mapear elementos si implementan entity()
            elseif (is_array($value) || $value instanceof \Traversable) {
                $mapped = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof \Cavesman\Model\Base) {
                        $mapped[$key] = $item->entity();
                    } else {
                        $mapped[$key] = $item;
                    }
                }
                $value = $mapped;
            }

            // Asignar valor si existe la propiedad en la entidad
            if (property_exists($entityInstance, $property->getName())) {
                $entityProperty = new \ReflectionProperty($entityInstance, $property->getName());
                $entityProperty->setAccessible(true);
                $entityProperty->setValue($entityInstance, $value);
            }
        }

        return $entityInstance;
    }
}
