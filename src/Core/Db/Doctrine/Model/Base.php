<?php

namespace Cavesman\Db\Doctrine\Model;

use Cavesman\Db\Doctrine\Interface\Entity;
use Cavesman\Exception\ModuleException;
use Cavesman\Model\Base as BaseModel;
use ReflectionException;

/**
 * Used when model requires ID
 */
abstract class Base extends BaseModel implements Entity
{

    /**
     * @throws ReflectionException
     * @throws ModuleException
     * @throws \Exception
     */
    public function entity(\Cavesman\Db\Doctrine\Entity\Base|string $entityClass, bool $update = false): ?\Cavesman\Db\Doctrine\Entity\Base
    {

        if (property_exists($this, 'id') && $this->id) {
            $entity = $entityClass::findOneBy(['id' => $this->id, 'deletedOn' => null]);
            if (!$entity)
                throw new \Exception('item.error.not-found', 404);

            if (!$update)
                return $entity;
        } else
            $entity = new $entityClass();

        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $property) {

            $value = $property->getValue($this);

            // Sí es una instancia de Base del modelo, convertirla a entidad
            if ($value instanceof Base)
                continue;

            // Si es array o Traversable, mapear elementos si implementan entity()
            elseif (is_array($value)) {
               continue;
            }

            // Asignar valor si existe la propiedad en la entidad
            if (property_exists($entity, $property->getName())) {
                $entityProperty = new \ReflectionProperty($entity, $property->getName());
                $entityProperty->setValue($entity, $value);
            }
        }

        return $entity;
    }
}
