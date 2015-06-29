<?php
namespace thing\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as m;

/** @m\Entity */
class Post {
	/**
	 * @m\Id
	 * @m\GeneratedValue(strategy="AUTO")
	 * @m\Column(type="integer")
	 */
	protected $id;

	/** @m\Column(type="string") */
	protected $title;

	/** @m\Column(type="text") */
	protected $url;

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
	 * @m\OneToMany(targetEntity="Comment", mappedBy="post", fetch="EXTRA_LAZY")
	 * @var Collection
	 */
	protected $comments;

	/**
	 * @m\ManyToOne(targetEntity="User", inversedBy="posts")
	 * @var User
	 */
	protected $poster;

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	public function __construct() {
		$this->comments = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return Post
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHostname() {
		return parse_url($this->getUrl(), PHP_URL_HOST);
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 * @return Post
	 */
	public function setUrl($url) {
		$this->url = $url;
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
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @param Comment $comm
	 * @param Comment|null $parent
	 * @return Post
	 */
	public function addComment(Comment $comm, Comment $parent = null) {
		if (null !== $parent) {
			$parent->addReply($comm);
		}
		$this->getComments()->add($comm);
		$comm->setPost($this);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCommentThreads() {
		return $this->getComments()->matching(
			Criteria::create()->where(Criteria::expr()->isNull('parent'))
		);
	}

	/**
	 * @return User
	 */
	public function getPoster() {
		return $this->poster;
	}

	/**
	 * @param User $user
	 * @return Post
	 */
	public function setPoster(User $user) {
		$this->poster = $user;
		return $this;
	}
}
