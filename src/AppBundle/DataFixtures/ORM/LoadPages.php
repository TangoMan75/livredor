<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Page;
use AppBundle\Entity\Section;
use AppBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPages implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Load Pages
        $pageCount = 10;
        for ($i = 0; $i < $pageCount; $i++) {

            $page = new Page();
            $page->setId($i)
                ->setTitle($faker->sentence(4, true))
                ->setSubTitle($faker->sentence(4, true))
                ->setDescription("<p>".$faker->text(mt_rand(600, 1200))."</p>");

            $manager->persist($page);

            // Load Sections
            $sectionCount = mt_rand(0, 10);
            for ($j = 0; $j < $sectionCount; $j++) {
                $section = new Section();
                $sectionLength = mt_rand(600, 2400);
                $text = "<p>".$faker->text($sectionLength)."</p>";
                $section->setPage($page)
                ->setTitle($faker->sentence(4, true))
                ->setSubTitle($faker->sentence(4, true))
                ->setDescription("<p>".$faker->text(mt_rand(600, 1200))."</p>");

                $manager->persist($section);
            }
        }

        $manager->flush();
    }
}