<?php

namespace Cavesman\Db\Doctrine\Entity;

use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Exception\ModuleException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use ReflectionClass;
use ReflectionException;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Base implements Db\Doctrine\Interface\Entity
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
     */
    public function model(string $modelClass): ?Db\Doctrine\Model\Base
    {
        /** @var ?Db\Doctrine\Model\Base $model */
        $model = new $modelClass();
        $entityReflection = new ReflectionClass($this);
        $modelReflection = new ReflectionClass($model);

        foreach ($modelReflection->getProperties() as $modelProp) {
            $propName = $modelProp->getName();

            if ($entityReflection->hasProperty($propName)) {
                $entityProp = $entityReflection->getProperty($propName);
                $entityProp->setAccessible(true);
                $value = $entityProp->getValue($this);

                if ($value instanceof Collection) {
                    $submodelClassname = $model->typeOfCollection($propName);
                    $items = [];
                    foreach ($value as $item) {
                        $items[] = method_exists($item, 'model') ? $item->model($submodelClassname) : $item;
                    }
                    $model->{$propName} = $items;
                } elseif ($value instanceof Base) {
                    $submodelClassname = $model->typeOfCollection($propName);
                    $model->{$propName} = method_exists($value, 'model') ? $value->model($submodelClassname) : $value;
                } else {
                    $model->{$propName} = $value;
                }
            }
        }

        return $model;
    }

}
