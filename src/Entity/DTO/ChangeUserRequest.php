<?php

namespace App\Entity\DTO;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * @ExclusionPolicy("all")
 *
 */
class ChangeUserRequest
{
    /**
     * @JMSSerializer\Type("integer")
     * @JMSSerializer\Expose
     * @Assert\NotNull
     *
     */
    private $id;

    /**
     * @JMSSerializer\Type("string")
     * @Assert\NotNull
     * @JMSSerializer\Expose
     *
     */
    private $role;

    /**
     * @JMSSerializer\Type("boolean")
     * @Assert\NotNull
     * @JMSSerializer\Expose
     *
     */
    private $isPromoting;

    public function getId()
    {
        return $this->id;
    }


    public function getIsPromoting()
    {
        return $this->isPromoting;
    }

    public function setIsPromoting($isPromoting)
    {
        $this->isPromoting = $isPromoting;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

}