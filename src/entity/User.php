<?php
namespace thing\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as m;

/** @m\Entity */
class User {
	/**
	 * @m\Id
	 * @m\GeneratedValue(strategy="AUTO")
	 * @m\Column(type="integer")
	 */
	protected $id;

	/**
	 * @m\Column(type="string")
	 */
	protected $username;

	/**
	 * @m\Column(type="string", length=60)
	 */
	protected $password;

	/**
	 * @m\Column(type="string")
	 */
	protected $email;

	/**
	 * @m\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @m\OneToMany(targetEntity="Comment", mappedBy="user", fetch="EXTRA_LAZY")
	 * @var Collection
	 */
	protected $comments;

	/**
	 * @m\OneToMany(targetEntity="Post", mappedBy="poster", fetch="EXTRA_LAZY")
	 * @var Collection
	 */
	protected $posts;

	public function __construct() {
		$this->comments = new ArrayCollection();
		$this->posts = new ArrayCollection();
	}

	public function __sleep() {
		return ['id', 'username', 'password', 'email', 'created'];
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $name
	 * @return User
	 */
	public function setUsername($name) {
		$this->username = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $password
	 * @return User
	 * @throws \Exception
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * @param string $password
	 * @return boolean
	 */
	public function isValidPassword($password) {
		return password_verify($password, $this->password);
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 * @return User
	 */
	public function setCreated(\DateTime $created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getPosts() {
		return $this->posts;
	}

	/**
	 * @return Collection
	 */
	public function getComments() {
		return $this->comments;
	}
}
