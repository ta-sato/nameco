<?php

namespace Nameco\User\SchedulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity
 */
class Area
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
     * @ORM\ManyToMany(targetEntity="Establishment", inversedBy="area")
     * @ORM\JoinTable(name="areas_establishments",
     *   joinColumns={
     *     @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     *   }
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $establishment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->establishment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Area
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
     * Set created
     *
     * @param \DateTime $created
     * @return Area
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
     * @return Area
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
     * Add establishment
     *
     * @param \Nameco\User\SchedulerBundle\Entity\Establishment $establishment
     * @return Area
     */
    public function addEstablishment(\Nameco\User\SchedulerBundle\Entity\Establishment $establishment)
    {
        $this->establishment[] = $establishment;

        return $this;
    }

    /**
     * Remove establishment
     *
     * @param \Nameco\User\SchedulerBundle\Entity\Establishment $establishment
     */
    public function removeEstablishment(\Nameco\User\SchedulerBundle\Entity\Establishment $establishment)
    {
        $this->establishment->removeElement($establishment);
    }

    /**
     * Get establishment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstablishment()
    {
        return $this->establishment;
    }
}