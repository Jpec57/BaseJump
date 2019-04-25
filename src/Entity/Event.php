<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as JMSSerializer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @JMSSerializer\ExclusionPolicy("all")
 *
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="createdEvents")
     * @Assert\NotNull
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("App\Entity\User")
     */
    private $creator;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $title;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array<DateTime<'d-m-Y'>>")
     * @ORM\Column(type="array")
     * @Assert\NotNull
     * @Assert\Count(
     *      min = 1,
     * )
     */
    private $dates = [];

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("DateTime<'d-m-Y'>")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("boolean")
     * @ORM\Column(type="boolean")
     * @Assert\NotNull
     */
    private $isAnswerVisible;
    /**
     * @JMSSerializer\Expose
     * @ORM\Column(type="boolean")
     * @JMSSerializer\Type("boolean")
     * @Assert\NotNull
     */
    private $enablesMultipleVotes;

    /**
     * @JMSSerializer\Expose
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="invitedEvents")
     * @Assert\Count(
     *      min = 1,
     *      max = 10
     * )
     */
    private $guests = [];



    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDates(): ?array
    {
        return $this->dates;
    }

    public function setDates(array $dates): self
    {
        $this->dates = $dates;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getEnablesMultipleVotes(): ?bool
    {
        return $this->enablesMultipleVotes;
    }

    public function setEnablesMultipleVotes(bool $enablesMultipleVotes): self
    {
        $this->enablesMultipleVotes = $enablesMultipleVotes;

        return $this;
    }

    public function getGuests()
    {
        return $this->guests;
    }

    public function setGuests(ArrayCollection $guests): self
    {
        $this->guests = $guests;

        return $this;
    }

    public function getisAnswerVisible()
    {
        return $this->isAnswerVisible;
    }

    public function setIsAnswerVisible($isAnswerVisible): void
    {
        $this->isAnswerVisible = $isAnswerVisible;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setCreator($creator): void
    {
        $this->creator = $creator;
    }
}
