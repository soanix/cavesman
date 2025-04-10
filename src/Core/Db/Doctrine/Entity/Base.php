<?php

namespace Cavesman\Db\Doctrine\Entity;

use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Exception\ModuleException;
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
     * @throws ModuleException
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
     * @throws ModuleException
     */
    public static function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return Db::getManager()->getRepository(static::class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    public function model(Db\Doctrine\Model\Base|string $entityClass): ?Db\Doctrine\Model\Base
    {

        $entity = new $entityClass();

        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {

            $value = $property->getValue($this);

            // Sí es una instancia de Base del modelo, convertirla a entidad
            if ($value instanceof Base)
                continue;

            // Si es array o Traversable, mapear elementos si implementan entity()
            elseif (is_array($value) || $value instanceof \Traversable) {
                $mapped = [];
                foreach ($value as $key => $item) {
                    if ($item instanceof Base)
                        continue;

                    $mapped[$key] = $item;
                }
                $value = $mapped;
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
