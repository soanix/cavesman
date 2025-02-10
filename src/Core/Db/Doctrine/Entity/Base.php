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
    abstract function model(): ?Db\Doctrine\Model\Base;
}
