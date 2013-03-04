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

//	/**
//	 * @var string
//	 *
//	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
//	 * @Expose
//	 * @SerializedName("text")
//	 */
//	private $name;
//
//	/**
//	 * @var string
//	 *
//	 * @ORM\Column(name="kana", type="string", length=255, nullable=false)
//	 */
//	private $kana;

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
	private $isAvtive;

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

//	/**
//	 * @var \Doctrine\Common\Collections\Collection
//	 *
//	 * @ORM\ManyToMany(targetEntity="User", inversedBy="user")
//	 * @ORM\JoinTable(name="bookmark",
//	 *   joinColumns={
//	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
//	 *   },
//	 *   inverseJoinColumns={
//	 *     @ORM\JoinColumn(name="bookmark_user_id", referencedColumnName="id")
//	 *   }
//	 * )
//	 */
//	private $bookmarkUser;

//	/**
//	 * @var \Doctrine\Common\Collections\Collection
//	 *
//	 * @ORM\ManyToMany(targetEntity="Schedule", mappedBy="user")
//	 */
//	private $schedule;

	/**
	 * @ORM\Column(name="family_name", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="{{ limit }}文字以内で入力してください")
	 * @var string
	 */
	private $family_name;

	/**
	 * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="{{ limit }}文字以内で入力してください")
	 * @var string
	 */
	private $first_name;

	/**
	 * @ORM\Column(name="kana_family", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="{{ limit }}文字以内で入力してください")
	 * @Assert\Regex(
	 * 		pattern="/^[ァ-ヾ]+$/u",
	 * 		match=true,
	 * 		message="カタカナで入力してください"
	 * )
	 * @var string
	 */
	private $kana_family;

	/**
	 * @ORM\Column(name="kana_first", type="string", length=50, nullable=false)
	 * 
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(limit=50, message="{{ limit }}文字以内で入力してください")
	 * @Assert\Regex(
	 * 		pattern="/^[ァ-ヾ]+$/u",
	 * 		match=true,
	 * 		message="カタカナで入力してください"
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
		$this->isAvtive = true;
		$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

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
		$this->password = $password;

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

//	/**
//	 * Set name
//	 *
//	 * @param string $name
//	 * @return User
//	 */
//	public function setName($name)
//	{
//		$this->name = $name;
//
//		return $this;
//	}
//
//	/**
//	 * Get name
//	 *
//	 * @return string
//	 */
//	public function getName()
//	{
//		return $this->name;
//	}

//	/**
//	 * Set kana
//	 *
//	 * @param string $kana
//	 * @return User
//	 */
//	public function setKana($kana)
//	{
//		$this->kana = $kana;
//
//		return $this;
//	}
//
//	/**
//	 * Get kana
//	 *
//	 * @return string
//	 */
//	public function getKana()
//	{
//		return $this->kana;
//	}

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
// 	public function setConfirm($confirm)
// 	{
// 		$this->confirm = $confirm;
// 		return $this;
// 	}
// 	public function getConfirm()
// 	{
// 		return $this->confirm;
// 	}

// 	/**
// 	 * set values bedore persist
// 	 *
// 	 * @ORM\PrePersist
// 	 */
// 	public function prePersist()
// 	{
// 		$this->created = new \DateTime();
// 		$this->updated = new \DateTime();

// 		$mail = preg_split('/@/', $this->email);
// 		$this->username = $mail[0];
// 	}

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
		return $this->isAvtive;
	}

	public function getKana()
	{
		return $this->getKanaFamily() . ' ' . $this->getKanaFirst();
	}
	
	public function getName()
	{
		return $this->getFamilyName() . ' ' . $this->getFirstName();
	}
//	
//	public function build($email, $password, $familyname, $firstname, $familyreading, $firstreading)
//	{
//		$this->setKana($familyreading . ' ' . $firstreading);
//		$this->setName($familyname . ' ' . $firstname);
//
//		// パスワードハッシュ化
////		$factory  = $this->get('security.encoder_factory');
////		$encoder  = $factory->getEncoder($user);
////		$hashedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
////		$this->setPassword($password);
//
//		// usernameを@前に設定
//		$mail = preg_split('/@/', $email);
//		$this->setUsername($mail[0]);
//	}
	public function encodePassword($container, $password)
	{
		$encoder = $container->get('security.encoder_factory')->getEncoder($this);
		$this->setPassword($encoder->encodePassword($password, $this->getSalt()));
	}

    /**
     * Set isAvtive
     *
     * @param boolean $isAvtive
     * @return User
     */
    public function setIsAvtive($isAvtive)
    {
        $this->isAvtive = $isAvtive;
    
        return $this;
    }

    /**
     * Get isAvtive
     *
     * @return boolean 
     */
    public function getIsAvtive()
    {
        return $this->isAvtive;
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