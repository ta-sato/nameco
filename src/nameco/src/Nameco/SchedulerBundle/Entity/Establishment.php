<?php

namespace Nameco\SchedulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Establishment
 *
 * @ORM\Table(name="establishment")
 * @ORM\Entity(repositoryClass="Nameco\SchedulerBundle\Repository\EstablishmentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Establishment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
	 * 
	 * @Assert\NotBlank()
	 * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Schedule", mappedBy="establishment")
     */
    private $schedule;

    /**
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="establishments")
	 * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedule = new \Doctrine\Common\Collections\ArrayCollection();
		$this->enabled = true;
    }


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
     * Set name
     *
     * @param string $name
     * @return Establishment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Establishment
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Establishment
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Establishment
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add schedule
     *
     * @param \Nameco\SchedulerBundle\Entity\Schedule $schedule
     * @return Establishment
     */
    public function addSchedule(\Nameco\SchedulerBundle\Entity\Schedule $schedule)
    {
        $this->schedule[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \Nameco\SchedulerBundle\Entity\Schedule $schedule
     */
    public function removeSchedule(\Nameco\SchedulerBundle\Entity\Schedule $schedule)
    {
        $this->schedule->removeElement($schedule);
    }

    /**
     * Get schedule
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set area
     *
     * @param \Nameco\SchedulerBundle\Entity\Area $area
     * @return Establishment
     */
    public function setArea(\Nameco\SchedulerBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Nameco\SchedulerBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    public function __toString()
    {
    	return $this->getName();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
    	if (!$this->getCreated())
    	{
    		$this->created = new \DateTime();
    	}
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
    	$this->updated = new \DateTime();
    }
}