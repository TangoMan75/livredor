<?php

namespace AppBundle\Entity\Relationships;

// tag
use AppBundle\Entity\Tag;
// media
use AppBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHaveMedias
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Media` entity to implement `$tags` property with `ManyToMany` and `inversedBy="medias"` annotation.
 * 2. Requires `Media` entity to implement `linkTag` and `unlinkTag` methods.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->medias = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait TagsHaveMedias
{
    /**
     * @var array|Media[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Media", mappedBy="tags")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $medias = [];

    /**
     * @param array|Media[]|ArrayCollection $medias
     *
     * @return $this
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * @return array|Media[]|ArrayCollection $medias
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function hasMedia(Media $media)
    {
        if ($this->medias->contains($media)) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function addMedia(Media $media)
    {
        $this->linkMedia($media);
        $media->linkTag($this);

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function removeMedia(Media $media)
    {
        $this->unlinkMedia($media);
        $media->unlinkTag($this);

        return $this;
    }

    /**
     * @param Media $media
     */
    public function linkMedia(Media $media)
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }
    }

    /**
     * @param Media $media
     */
    public function unlinkMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }
}
