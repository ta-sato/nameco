<?php

namespace Nameco\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity
 */
class Schedule
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
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_datetime", type="datetime", nullable=false)
     */
    private $startDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_datetime", type="datetime", nullable=false)
     */
    private $endDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="detail", type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="out", type="boolean", nullable=false)
     */
    private $out;

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
     * @ORM\ManyToMany(targetEntity="Establishment", inversedBy="schedule")
     * @ORM\JoinTable(name="schedules_establishments",
     *   joinColumns={
     *     @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     *   }
     * )
     */
    private $establishment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="schedule")
     * @ORM\JoinTable(name="schedules_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   }
     * )
     */
    private $user;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id")
     * })
     */
    private $ownerUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->establishment = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Schedule
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDatetime
     *
     * @param \DateTime $startDatetime
     * @return Schedule
     */
    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;
    
        return $this;
    }

    /**
     * Get startDatetime
     *
     * @return \DateTime 
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Set endDatetime
     *
     * @param \DateTime $endDatetime
     * @return Schedule
     */
    public function setEndDatetime($endDatetime)
    {
        $this->endDatetime = $endDatetime;
    
        return $this;
    }

    /**
     * Get endDatetime
     *
     * @return \DateTime 
     */
    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return Schedule
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    
        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set out
     *
     * @param boolean $out
     * @return Schedule
     */
    public function setOut($out)
    {
        $this->out = $out;
    
        return $this;
    }

    /**
     * Get out
     *
     * @return boolean 
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Schedule
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
     * @return Schedule
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
     * @param \Nameco\SecurityBundle\Entity\Establishment $establishment
     * @return Schedule
     */
    public function addEstablishment(\Nameco\SecurityBundle\Entity\Establishment $establishment)
    {
        $this->establishment[] = $establishment;
    
        return $this;
    }

    /**
     * Remove establishment
     *
     * @param \Nameco\SecurityBundle\Entity\Establishment $establishment
     */
    public function removeEstablishment(\Nameco\SecurityBundle\Entity\Establishment $establishment)
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

    /**
     * Add user
     *
     * @param \Nameco\SecurityBundle\Entity\User $user
     * @return Schedule
     */
    public function addUser(\Nameco\SecurityBundle\Entity\User $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \Nameco\SecurityBundle\Entity\User $user
     */
    public function removeUser(\Nameco\SecurityBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ownerUser
     *
     * @param \Nameco\SecurityBundle\Entity\User $ownerUser
     * @return Schedule
     */
    public function setOwnerUser(\Nameco\SecurityBundle\Entity\User $ownerUser = null)
    {
        $this->ownerUser = $ownerUser;
    
        return $this;
    }

    /**
     * Get ownerUser
     *
     * @return \Nameco\SecurityBundle\Entity\User 
     */
    public function getOwnerUser()
    {
        return $this->ownerUser;
    }
}