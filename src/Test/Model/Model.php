<?php

namespace Cavesman\Test\Model;

use Cavesman\Db\Doctrine\Model\Base;
use Cavesman\Test\Entity\Entity;
use DateTime;

class Model extends Base
{
    const \Cavesman\Db\Doctrine\Entity\Base|string ENTITY = Entity::class;

    public string $name = '';
    public DateTime|string|null $date = null;

    /**
     * @var Model[]
     */
    public array $children = [];


    public function typeOfCollection(string $property): string
    {
        return match ($property) {

            'children' => Model::class,
            // Agrega tus casos aquí
            default => throw new \RuntimeException('No model mapping for ' . $property),
        };
    }

}
