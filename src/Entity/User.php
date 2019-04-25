<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * User
 * @ORM\Table("user")
 * @ORM\Entity
 * @JMSSerializer\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SWG\Property(description="The unique identifier of the user.")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="creator")
     */
    protected $createdEvents;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="guests", fetch="EAGER")
     */
    protected $invitedEvents;
    /**
     * @Groups({"user"})
     * @SWG\Property(type="string", maxLength=255)
     */
    protected $username;
    /**
     * @Groups({"user"})
     */
    protected $password;

    /**
     * @Groups({"user:read"})
     */
    protected $roles;
    /**
     * @Groups({"user"})
     */
    protected $email;
    /**
     * @Groups({"user:write"})
     */
    protected $plainPassword;


    public function __construct()
    {
        $this->invitedEvents = new ArrayCollection();
        $this->createdEvents = new ArrayCollection();
    }

    //region Getter/Setter

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param mixed $events
     */
    public function setEvents($events): void
    {
        $this->events = $events;
    }

    /**
     * @return mixed
     */
    public function getCreatedEvents()
    {
        return $this->createdEvents;
    }

    /**
     * @param mixed $createdEvents
     */
    public function setCreatedEvents($createdEvents): void
    {
        $this->createdEvents = $createdEvents;
    }

    /**
     * @return mixed
     */
    public function getInvitedEvents()
    {
        return $this->invitedEvents;
    }

    /**
     * @param mixed $invitedEvents
     */
    public function setInvitedEvents($invitedEvents): void
    {
        $this->invitedEvents = $invitedEvents;
    }

    //endregion

    public function getPersoEventsWithDeadline(bool $isUpcoming){
        if ( $isUpcoming ){
            $criteria = Criteria::create()
                ->where(Criteria::expr()->gt("deadline", new \DateTime()));
        } else {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->lt("deadline", new \DateTime()));
        }
        $events = $this->getCreatedEvents()->matching($criteria);
        return $events;
    }

    public function getInvitedEventsWithDeadline(bool $isUpcoming){
        if ( $isUpcoming ){
            $criteria = Criteria::create()
                ->where(Criteria::expr()->gt("deadline", new \DateTime()));
        } else {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->lt("deadline", new \DateTime()));
        }
        $events = $this->invitedEvents->matching($criteria);
        return $events;
    }

}