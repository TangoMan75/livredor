<?php

namespace AppBundle\Entity\Relationships;

// post
use AppBundle\Entity\Post;
// user
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasPosts
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 *
 * 1. Requires `Post` entity to implement `$user` property with `ManyToOne` and `inversedBy="posts"` annotation.
 * 2. Requires `Post` entity to implement linkUser(User $user) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasPosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts = [];

    /**
     * @param array|Post[]|ArrayCollection $posts
     *
     * @return $this
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * @return array|Post[]|ArrayCollection $posts
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    public function hasPost(Post $post)
    {
        if ($this->posts->contains($post)) {
            return true;
        }

        return false;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function addPost(Post $post)
    {
        $this->linkPost($post);
        $post->linkUser($this);

        return $this;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function removePost(Post $post)
    {
        $this->unlinkPost($post);
        $post->unlinkUser();

        return $this;
    }

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
        }
    }

    /**
     * @param Post $post
     */
    public function unlinkPost(Post $post)
    {
        $this->posts->removeElement($post);
    }
}