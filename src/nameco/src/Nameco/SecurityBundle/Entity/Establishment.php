<?php

namespace Nameco\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Establishment
 *
 * @ORM\Table(name="establishment")
 * @ORM\Entity
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
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
     * @ORM\ManyToMany(targetEntity="Area", mappedBy="establishment")
     */
    private $area;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Schedule", mappedBy="establishment")
     */
    private $schedule;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->area = new \Doctrine\Common\Collections\ArrayCollection();
        $this->schedule = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add area
     *
     * @param \Nameco\SecurityBundle\Entity\Area $area
     * @return Establishment
     */
    public function addArea(\Nameco\SecurityBundle\Entity\Area $area)
    {
        $this->area[] = $area;
    
        return $this;
    }

    /**
     * Remove area
     *
     * @param \Nameco\SecurityBundle\Entity\Area $area
     */
    public function removeArea(\Nameco\SecurityBundle\Entity\Area $area)
    {
        $this->area->removeElement($area);
    }

    /**
     * Get area
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Add schedule
     *
     * @param \Nameco\SecurityBundle\Entity\Schedule $schedule
     * @return Establishment
     */
    public function addSchedule(\Nameco\SecurityBundle\Entity\Schedule $schedule)
    {
        $this->schedule[] = $schedule;
    
        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \Nameco\SecurityBundle\Entity\Schedule $schedule
     */
    public function removeSchedule(\Nameco\SecurityBundle\Entity\Schedule $schedule)
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
}