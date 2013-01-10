<?php

namespace Nameco\Admin\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
	public function eraseCredentials()
	{
	}

	public function getRoles()
	{
	}

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
	 * @ORM\Column(name="username", type="string", length=255, nullable=false)
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255, nullable=false)
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
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="kana", type="string", length=255, nullable=false)
	 */
	private $kana;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=255, nullable=false)
	 */
	private $email;

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
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="user")
	 * @ORM\JoinTable(name="bookmark",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="bookmark_user_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	private $bookmarkUser;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Schedule", mappedBy="user")
	 */
	private $schedule;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->bookmarkUser = new \Doctrine\Common\Collections\ArrayCollection();
		$this->schedule = new \Doctrine\Common\Collections\ArrayCollection();

		$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

		// PrePersistが発動しない...
		$this->created = new \DateTime();
		$this->updated = new \DateTime();
	}


	/**
	 * Set username
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
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

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return User
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
	 * Set kana
	 *
	 * @param string $kana
	 * @return User
	 */
	public function setKana($kana)
	{
		$this->kana = $kana;

		return $this;
	}

	/**
	 * Get kana
	 *
	 * @return string
	 */
	public function getKana()
	{
		return $this->kana;
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
	 * Set enabled
	 *
	 * @param boolean $enabled
	 * @return User
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

	/**
	 * Add schedule
	 *
	 * @param \Nameco\Admin\UserBundle\Entity\Schedule $schedule
	 * @return User
	 */
	public function addSchedule(\Nameco\Admin\UserBundle\Entity\Schedule $schedule)
	{
		$this->schedule[] = $schedule;

		return $this;
	}

	/**
	 * Remove schedule
	 *
	 * @param \Nameco\Admin\UserBundle\Entity\Schedule $schedule
	 */
	public function removeSchedule(\Nameco\Admin\UserBundle\Entity\Schedule $schedule)
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
	 * Add bookmarkUser
	 *
	 * @param \Nameco\Admin\UserBundle\Entity\User $bookmarkUser
	 * @return User
	 */
	public function addBookmarkUser(\Nameco\Admin\UserBundle\Entity\User $bookmarkUser)
	{
		$this->bookmarkUser[] = $bookmarkUser;

		return $this;
	}

	/**
	 * Remove bookmarkUser
	 *
	 * @param \Nameco\Admin\UserBundle\Entity\User $bookmarkUser
	 */
	public function removeBookmarkUser(\Nameco\Admin\UserBundle\Entity\User $bookmarkUser)
	{
		$this->bookmarkUser->removeElement($bookmarkUser);
	}

	/**
	 * Get bookmarkUser
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getBookmarkUser()
	{
		return $this->bookmarkUser;
	}

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
}