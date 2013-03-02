<?php

namespace Nameco\SchedulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="Nameco\SchedulerBundle\Repository\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"compareDateTime"})
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
     * @Assert\NotBlank(message="必須項目です")
     * @Assert\MaxLength(limit=50, message="{{ limit }}文字以内で入力してください")
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_datetime", type="datetime", nullable=false)
     * @Assert\NotBlank(message="必須項目です")
     */
    private $startDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_datetime", type="datetime", nullable=false)
     * @Assert\NotBlank(message="必須項目です")
     */
    private $endDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="detail", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="必須項目です")
     * @Assert\MaxLength(limit=255, message="{{ limit }}文字以内で入力してください")
     */
    private $detail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="`out`", type="boolean", nullable=false)
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
     * @ORM\ManyToMany(targetEntity="Nameco\UserBundle\Entity\User", inversedBy="schedule")
     * @ORM\JoinTable(name="schedules_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   }
     * )
     * @Assert\Count(min=1, minMessage="参加者を選んでください")
     */
    private $user;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Nameco\UserBundle\Entity\User")
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
     * @param \Nameco\User\EstablishmentBundle\Entity\Establishment $establishment
     * @return Schedule
     */
    public function addEstablishment(\Nameco\SchedulerBundle\Entity\Establishment $establishment)
    {
        $this->establishment[] = $establishment;

        return $this;
    }

    /**
     * Remove establishment
     *
     * @param \Nameco\SchedulerBundle\Entity\Establishment $establishment
     */
    public function removeEstablishment(\Nameco\SchedulerBundle\Entity\Establishment $establishment)
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
     * @param \Nameco\UserBundle\Entity\User $user
     * @return Schedule
     */
    public function addUser(\Nameco\UserBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Nameco\UserBundle\Entity\User $user
     */
    public function removeUser(\Nameco\UserBundle\Entity\User $user)
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
     * @param \Nameco\UserBundle\Entity\User $ownerUser
     * @return Schedule
     */
    public function setOwnerUser(\Nameco\UserBundle\Entity\User $ownerUser = null)
    {
        $this->ownerUser = $ownerUser;

        return $this;
    }

    /**
     * Get ownerUser
     *
     * @return \Nameco\UserBundle\Entity\User
     */
    public function getOwnerUser()
    {
        return $this->ownerUser;
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

    public function compareDateTime(ExecutionContext $context)
    {
        // 日付の整合性チェック
        $start = $this->getStartDatetime();
        $end = $this->getEndDatetime();
        if ($start != null && $end != null
                && ($start >= $end))
        {
            $context->addViolationAtSubPath('startDateTime', '開始日時が終了日時以降に設定されています');
        }
    }

}