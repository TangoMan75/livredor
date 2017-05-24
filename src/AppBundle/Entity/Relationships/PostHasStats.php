<?php

namespace AppBundle\Entity\Relationships;

// stat
use AppBundle\Entity\Stat;
// post
use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PostHasStats
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 *
 * 1. Requires `Stat` entity to implement `$post` property with `ManyToOne` and `inversedBy="stats"` annotation.
 * 2. Requires `Stat` entity to implement linkPost(Post $post) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->stats = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PostHasStats
{
    /**
     * @var array|Stat[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stat", mappedBy="post", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $stats = [];

    /**
     * @param array|Stat[]|ArrayCollection $stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @return array|Stat[]|ArrayCollection $stats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param Stat $stat
     *
     * @return bool
     */
    public function hasStat(Stat $stat)
    {
        if ($this->stats->contains($stat)) {
            return true;
        }

        return false;
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function addStat(Stat $stat)
    {
        $this->linkStat($stat);
        $stat->linkPost($this);

        return $this;
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function removeStat(Stat $stat)
    {
        $this->unlinkStat($stat);
        $stat->unlinkPost($this);

        return $this;
    }

    /**
     * @param Stat $stat
     */
    public function linkStat(Stat $stat)
    {
        if (!$this->stats->contains($stats)) {
            $this->stats[] = $stat;
        }
    }

    /**
     * @param Stat $stat
     */
    public function unlinkStat(Stat $stat)
    {
        $this->stats->removeElement($stat);
    }
}
