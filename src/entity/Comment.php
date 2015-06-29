<?php
namespace thing\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as m;

/** @m\Entity */
class Comment {
	/**
	 * @m\Id
	 * @m\GeneratedValue(strategy="AUTO")
	 * @m\Column(type="integer")
	 */
	protected $id;

	/**
	 * @m\ManyToOne(targetEntity="Post", inversedBy="comments")
	 * @var Post
	 */
	protected $post;

	/**
	 * @m\ManyToOne(targetEntity="Comment", inversedBy="id")
	 * @var Comment
	 */
	protected $parent;

	/** @m\Column(type="text") */
	protected $message;

	/**
	 * @m\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @m\Column(type="datetime", nullable=true)
	 * @var \DateTime
	 */
	protected $updated;

	/**
	 * @m\ManyToOne(targetEntity="User", inversedBy="comments")
	 * @var User
	 */
	protected $user;

	/**
	 * @m\OneToMany(targetEntity="Comment", mappedBy="parent", fetch="EXTRA_LAZY")
	 * @var Collection
	 */
	protected $replies;

	public function __construct() {
		$this->replies = new ArrayCollection();
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Post
	 */
	public function getPost() {
		if (!$this->post && $this->parent) {
			return $this->parent->getPost();
		}
		return $this->post;
	}

	/**
	 * @param Post $post
	 * @return Comment
	 */
	public function setPost(Post $post) {
		$this->post = $post;
		return $this;
	}

	/**
	 * @return Comment
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param Comment $parent
	 * @return Comment
	 */
	public function setParent(Comment $parent) {
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param string $message
	 * @return Comment
	 */
	public function setMessage($message) {
		$this->message = $message;
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
	 * @return Post
	 */
	public function setCreated(\DateTime $created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 * @return Post
	 */
	public function setUpdated(\DateTime $updated) {
		$this->updated = $updated;
		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getReplies() {
		return $this->replies;
	}

	/**
	 * @param Comment $comm
	 * @return Comment
	 */
	public function addReply(Comment $comm) {
		$comm->setParent($this);
		$this->getReplies()->add($comm);
		return $this;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 * @return Comment
	 */
	public function setUser(User $user) {
		$this->user = $user;
		return $this;
	}
}
