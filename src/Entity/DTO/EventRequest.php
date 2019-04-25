<?php
/**
 * Created by PhpStorm.
 * User: jpbella
 * Date: 20/02/19
 * Time: 17:52
 */

namespace App\Entity\DTO;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMSSerializer;


class EventRequest
{
    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     */
    private $creator;

    /**
     * @Assert\NotNull
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $title;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $description;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $location;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array<DateTime<'d-m-Y'>>")
     * @Assert\Count(
     *      min = 1,
     * )
     */
    private $dates = [];

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("DateTime<'d-m-Y'>")
     */
    private $deadline;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("boolean")
     * @Assert\NotNull
     * @JMSSerializer\SerializedName("isAnswerVisible")
     */
    private $isAnswerVisible;
    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("boolean")
     * @Assert\NotNull
     * @JMSSerializer\SerializedName("enablesMultipleVotes")
     */
    private $enablesMultipleVotes;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array<integer>")
     * @Assert\Count(
     *      min = 1,
     *      max = 10
     * )
     *
     */
    private $guests = [];

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @param mixed $dates
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    }

    /**
     * @return mixed
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param mixed $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * @return mixed
     */
    public function getisAnswerVisible()
    {
        return $this->isAnswerVisible;
    }

    /**
     * @param mixed $isAnswerVisible
     */
    public function setIsAnswerVisible($isAnswerVisible)
    {
        $this->isAnswerVisible = $isAnswerVisible;
    }

    /**
     * @return mixed
     */
    public function getEnablesMultipleVotes()
    {
        return $this->enablesMultipleVotes;
    }

    /**
     * @param mixed $enablesMultipleVotes
     */
    public function setEnablesMultipleVotes($enablesMultipleVotes)
    {
        $this->enablesMultipleVotes = $enablesMultipleVotes;
    }

    /**
     * @return mixed
     */
    public function getGuests()
    {
        return $this->guests;
    }

    /**
     * @param mixed $guests
     */
    public function setGuests($guests)
    {
        $this->guests = $guests;
    }

}