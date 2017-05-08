<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Categorized
 * This class is designed to provide a simple and straitforward way to categorize entities.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Categorized
{
    /**
     * @var array|ArrayCollection
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $categories = [];

    /**
     * @var array
     */
    private $assoc = [
        'csv'  => 'document',
        'doc'  => 'document',
        'ods'  => 'document',
        'odt'  => 'document',
        'pdf'  => 'document',
        'pptx' => 'document',
        'txt'  => 'document',
        'xls'  => 'document',
        'gif'  => 'image',
        'jpeg' => 'image',
        'jpg'  => 'image',
        'png'  => 'image',
    ];

    /**
     * @param array $category
     *
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return bool
     */
    public function hasCategory($category)
    {
        if (in_array($category, (array)$this->categories)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function addCategory($category)
    {
        $this->checkCategory($category);

        foreach ($this->assoc as $type => $assoc) {
            if ($category == $type) {
                $this->checkCategory($assoc);
            }
        }

        return $this;
    }

    /**
     * @param string $category
     */
    public function checkCategory($category)
    {
        if (!in_array($category, (array)$this->categories)) {
            $this->categories[] = $category;
        }
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function removeCategory($category)
    {
        $this->categories->removeElement($category);

        return $this;
    }
}