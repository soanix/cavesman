<?php

namespace Cavesman\Db\Doctrine\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Entity extends Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: "integer")]
    public ?int $id = null;

    #[ORM\Column(name: "created_on", type: "datetime", nullable: true)]
    public ?DateTime $createdOn = null;

    #[ORM\Column(name: "updated_on", type: "datetime", nullable: true)]
    public ?\DateTime $updatedOn = null;


    #[ORM\Column(name: "deleted_on", type: "datetime", nullable: true)]
    public ?DateTime $deletedOn = null;


    #[ORM\PrePersist]
    public function onCreate(): void {
        $this->createdOn = new \DateTime();
    }
    #[ORM\PreUpdate]
    public function onUpdate(): void {
        $this->updatedOn = new \DateTime();
    }


}
