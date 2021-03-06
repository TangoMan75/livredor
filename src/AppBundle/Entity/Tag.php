<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\Traits\HasLabel;
use TangoMan\EntityHelper\Traits\HasName;
use TangoMan\EntityHelper\Traits\HasType;
use TangoMan\EntityHelper\Traits\Slugify;
use TangoMan\RelationshipBundle\Traits\HasRelationships;

/**
 * Class Tag
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
{

    use HasLabel;
    use HasName;
    use HasRelationships;
    use HasType;
    use Slugify;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Page[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="tags",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages;

    /**
     * @var Post[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="tags",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts;

    /**
     * @var Section[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="tags",
     *                                                          cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections;

    /**
     * @var Site[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Site", mappedBy="tags",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sites;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->pages    = new ArrayCollection();
        $this->posts    = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->sites    = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        if ( ! $this->type) {
            $this->setType(strtolower($this->name));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}