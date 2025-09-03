<?php

namespace Cavesman\Test\Model;

use Cavesman\Db\Doctrine\Model\Base;
use Cavesman\Test\Entity\Entity;
use DateTime;

class ModelBase extends Base
{
    const \Cavesman\Db\Doctrine\Entity\Base|string ENTITY = Entity::class;

    public string $name = '';
    public DateTime|string|null $date = null;



    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {

            'children' => ModelBase::class,
            // Agrega tus casos aquí
            default => null,
        };
    }

}
