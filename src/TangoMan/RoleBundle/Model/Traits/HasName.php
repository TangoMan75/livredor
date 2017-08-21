<?php

namespace TangoMan\RoleBundle\Model\Traits;

/**
 * Trait HasName
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\RoleBundle\Model\Traits
 */
trait HasName
{
    /**
     * @var String
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
