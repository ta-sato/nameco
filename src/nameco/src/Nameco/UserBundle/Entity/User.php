<?php

namespace Nameco\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Nameco\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="email", message="既に登録されています")
 * @ExclusionPolicy("ALL")
 */
class User implements AdvancedUserInterface
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @Expose
	 * @SerializedName("value")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255, nullable=false)
	 * @Assert\MinLength(limit=6, message="{{ limit }} 文字以上で入力してください")
	 * @Assert\NotBlank()
	 */
	private $password;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="salt", type="string", length=40, nullable=false)
	 */
	private $salt;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=255, nullable=false, unique=true)
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_active", type="boolean", nullable=false)
	 */
	private $isActive;

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
	 * @ORM\Column(name="family_name", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="姓は{{ limit }}文字以内で入力してください")
	 * @var string
	 */
	private $family_name;

	/**
	 * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="名は{{ limit }}文字以内で入力してください")
	 * @var string
	 */
	private $first_name;

	/**
	 * @ORM\Column(name="kana_family", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="姓は{{ limit }}文字以内で入力してください")
	 * @Assert\Regex(
	 * 		pattern="/^[ァ-ヾ]+$/u",
	 * 		match=true,
	 * 		message="姓はカタカナで入力してください"
	 * )
	 * @var string
	 */
	private $kana_family;

	/**
	 * @ORM\Column(name="kana_first", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="名は{{ limit }}文字以内で入力してください")
	 * @Assert\Regex(
	 * 		pattern="/^[ァ-ヾ]+$/u",
	 * 		match=true,
	 * 		message="名はカタカナで入力してください"
	 * )
	 * @var string
	 */
	private $kana_first;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
	 */
	private $roles;

	/**
	 * Constructor
	 */
	public function __construct()
	{
//		$this->bookmarkUser = new \Doctrine\Common\Collections\ArrayCollection();
//		$this->schedule = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
        $this->salt     = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

		// PrePersistが発動しない...
		$this->created = new \DateTime();
		$this->updated = new \DateTime();
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->getEmail();
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 * @return User
	 */
	public function setPassword($password)
	{
		if (isset($password))
		{
			$this->password = $password;
		}

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set salt
	 *
	 * @param string $salt
	 * @return User
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;

		return $this;
	}

	/**
	 * Get salt
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return User
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
	 * @return User
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
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

//	/**
//	 * Add schedule
//	 *
//	 * @param \Nameco\SchedulerBundle\Entity\Schedule $schedule
//	 * @return User
//	 */
//	public function addSchedule(\Nameco\SchedulerBundle\Entity\Schedule $schedule)
//	{
//		$this->schedule[] = $schedule;
//
//		return $this;
//	}
//
//	/**
//	 * Remove schedule
//	 *
//	 * @param \Nameco\SchedulerBundle\Entity\Schedule $schedule
//	 */
//	public function removeSchedule(\Nameco\SchedulerBundle\Entity\Schedule $schedule)
//	{
//		$this->schedule->removeElement($schedule);
//	}
//
//	/**
//	 * Get schedule
//	 *
//	 * @return \Doctrine\Common\Collections\Collection
//	 */
//	public function getSchedule()
//	{
//		return $this->schedule;
//	}

//	/**
//	 * Add bookmarkUser
//	 *
//	 * @param \Nameco\UserBundle\Entity\User $bookmarkUser
//	 * @return User
//	 */
//	public function addBookmarkUser(\Nameco\UserBundle\Entity\User $bookmarkUser)
//	{
//		$this->bookmarkUser[] = $bookmarkUser;
//
//		return $this;
//	}
//
//	/**
//	 * Remove bookmarkUser
//	 *
//	 * @param \Nameco\UserBundle\Entity\User $bookmarkUser
//	 */
//	public function removeBookmarkUser(\Nameco\UserBundle\Entity\User $bookmarkUser)
//	{
//		$this->bookmarkUser->removeElement($bookmarkUser);
//	}
//
//	/**
//	 * Get bookmarkUser
//	 *
//	 * @return \Doctrine\Common\Collections\Collection
//	 */
//	public function getBookmarkUser()
//	{
//		return $this->bookmarkUser;
//	}

	public function setFirstName($first_name)
	{
		$this->first_name = $first_name;
		return $this;
	}
	public function getFirstName()
	{
		return $this->first_name;
	}
	public function setFamilyName($family_name)
	{
		$this->family_name = $family_name;
		return $this;
	}
	public function getFamilyName()
	{
		return $this->family_name;
	}
	public function setKanaFamily($kana_family)
	{
		$this->kana_family = $kana_family;
		return $this;
	}
	public function getKanaFamily()
	{
		return $this->kana_family;
	}
	public function setKanaFirst($kana_first)
	{
		$this->kana_first = $kana_first;
		return $this;
	}
	public function getKanaFirst()
	{
		return $this->kana_first;
	}

	/**
	 * set values bedore update
	 *
	 * @ORM\PreUpdate
	 */
	public function preUpdate()
	{
		$this->updated = new \DateTime();
	}

	public function eraseCredentials()
	{
	}

	public function setRoles($roles)
    {
    	$this->roles = $roles;
    	return $this;
    }

	public function getRoles()
    {
    	return $this->roles->toArray();
    }

    public function getUserRoles()
    {
    	return $this->roles;
    }
    public function setUserRoles($roles)
    {
    	return $this->setRoles($roles);
    }
	
    public function __toString()
    {
    	return $this->getName();
    }

	public function isAccountNonExpired() {
		return true;
	}

	public function isAccountNonLocked() {
		return true;
	}

	public function isCredentialsNonExpired() {
		return true;
	}

	public function isEnabled() {
		return $this->isActive;
	}

	public function getKana()
	{
		return $this->getKanaFamily() . ' ' . $this->getKanaFirst();
	}
	
	public function getName()
	{
		return $this->getFamilyName() . ' ' . $this->getFirstName();
	}

	public function encodePassword($container, $password)
	{
		$encoder = $container->get('security.encoder_factory')->getEncoder($this);
		$this->setPassword($encoder->encodePassword($password, $this->getSalt()));
	}

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add roles
     *
     * @param \Nameco\UserBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Nameco\UserBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Nameco\UserBundle\Entity\Role $roles
     */
    public function removeRole(\Nameco\UserBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }
//
//	public function serialize() {
//		return serialize(array(
//			$this->id,
//			));		
//	}
//
//	public function unserialize($serialized) {
//		list (
//            $this->id,
//        ) = unserialize($serialized);		
//	}
}