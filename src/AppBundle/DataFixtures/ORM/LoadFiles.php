<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadFiles
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class LoadFiles implements FixtureInterface, ContainerAwareInterface,
                           OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 140;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR');

        $rootdir = $this->container->getParameter('kernel.root_dir')."/../web";
        // Get pptx
        $pptx = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir."/uploads/documents/*.{pptx,PPTX}", GLOB_BRACE)
        );

        // Get pdf
        $pdf = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir."/uploads/documents/*.{pdf,PDF}", GLOB_BRACE)
        );

        for ($i = 0; $i < count($pptx); $i++) {
            $doc = new Post();
            $doc
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setDocumentFileName($pptx[$i])
                ->setPublished($i % 2)
                ->setText($faker->text(mt_rand(100, 255)))
                ->setTitle($faker->sentence(4, true))
                ->setType('pptx')
                ->setViews(mt_rand(0, 100));
            $em->persist($doc);
        }

        for ($i = 0; $i < count($pdf); $i++) {
            $doc = new Post();
            $doc
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setDocumentFileName($pdf[$i])
                ->setPublished($i % 2)
                ->setText($faker->text(mt_rand(100, 255)))
                ->setTitle($faker->sentence(4, true))
                ->setType('pdf')
                ->setViews(mt_rand(0, 100));
            $em->persist($doc);
        }

        $em->flush();
    }
}
