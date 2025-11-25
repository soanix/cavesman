<?php

namespace Cavesman\Db\Doctrine\Entity;

use Cassandra\Date;
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


    /**
     * Marks the entity as deleted by assigning the current date to `deletedOn`.
     *
     * Important:
     *  - This method does **not** physically delete the entity from the database.
     *  - To complete the soft delete process, you must persist and flush the entity:
     *
     *    $entity->delete();
     *    $em->persist($entity);
     *    $em->flush();
     *
     * Extending:
     *  If the class is extended, this method can be overridden to add additional
     *  logic before or after the base soft-delete behavior. Example:
     *
     *    public function delete(): self
     *    {
     *        parent::delete();                 // Execute the base delete logic
     *        $this->buildings = new ArrayCollection(); // Additional logic
     *        return $this;
     *    }
     *
     * @param DateTime $date Deleted datetime object
     * @return self
     */
    public function delete(DateTime $date = new DateTime()): self
    {
        $this->deletedOn = $date;
        return $this;
    }


    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->createdOn = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedOn = new \DateTime();
    }


}
