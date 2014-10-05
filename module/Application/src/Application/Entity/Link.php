<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as m;

/** @m\Entity */
class Link {
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
     * @m\OneToMany(targetEntity="Comment", mappedBy="link", fetch="EXTRA_LAZY")
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $comments;

    /**
     * @m\ManyToOne(targetEntity="User", inversedBy="posts")
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
     * @return Link
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * @param string $url
     * @return Link
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
     * @return Link
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
     * @return Link
     */
    public function setUpdated(\DateTime $updated) {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * @param Comment      $comm
     * @param Comment|null $parent
     * @return Link
     */
    public function addComment(Comment $comm, Comment $parent = null) {
        if (null !== $parent) {
            $parent->addReply($comm);
        }
        $this->getComments()->add($comm);
        $comm->setLink($this);
        return $this;
    }

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
     * #return Link
     */
    public function setPoster(User $user) {
        $this->poster = $user;
        return $this;
    }
}
