<?php

namespace Cavesman\Test\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'entity')]
class Entity extends \Cavesman\Db\Doctrine\Entity\Entity
{
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    public string $name = '';

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    public ?self $parent = null;


    #[ORM\Column(name: 'date', type: 'datetime', nullable: true)]
    public ?DateTime $date = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    public Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }


}
