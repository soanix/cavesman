<?php

namespace app\Modules\Customer\Entity;

/**
 * CustomerEntity
 */
class CustomerEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var \DateTime
     */
    private $dateStart;

    /**
     * @var \DateTime
     */
    private $dateEnd;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     */
    private $dateModified;

    /**
     * @var \app\Modules\Customer\Entity\CustomerStatusEntity
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $calls;

    /**
     * @var \app\Modules\User\Entity\UserEntity
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calls = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
