<?php

namespace Cavesman\Db\Doctrine\Entity;

use Cavesman\Db;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ReflectionException;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Base
{

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return static|null
     */
    public static function findOneBy(array $criteria, ?array $orderBy = null): ?static
    {
        return Db::getManager()->getRepository(static::class)->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array|static[]
     */
    public static function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return Db::getManager()->getRepository(static::class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return Base|null
     * @throws ReflectionException
     */
    public function model(): ?\Cavesman\Model\Base
    {
        $entityClass = get_class($this); // Ej: App\Entity\Enterprise
        $modelClass = str_replace('App\\Entity', 'App\\Model', $entityClass); // App\Model\Enterprise

        if (!class_exists($modelClass)) {
            return null;
        }

        $modelInstance = new $modelClass();

        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            // Si es una instancia de Base, convertirla a modelo
            if ($value instanceof \Cavesman\Db\Doctrine\Entity\Base) {
                $value = $value->model();
            }

            // Si es un array o ArrayCollection, mapear los elementos si son instancia de Base
            elseif (is_array($value) || $value instanceof \Traversable) {
                $mapped = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof \Cavesman\Db\Doctrine\Entity\Base) {
                        $mapped[$key] = $item->model();
                    } else {
                        $mapped[$key] = $item;
                    }
                }
                $value = $mapped;
            }

            // Asignar valor si existe en el modelo
            if (property_exists($modelInstance, $property->getName())) {
                $modelProperty = new \ReflectionProperty($modelInstance, $property->getName());
                $modelProperty->setAccessible(true);
                $modelProperty->setValue($modelInstance, $value);
            }
        }

        return $modelInstance;
    }
}
