<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminEditTagType
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class AdminEditTagType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::Class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'type',
                TextType::Class,
                [
                    'label'    => 'Type',
                    'required' => false,
                ]
            )
            ->add(
                'label',
                ChoiceType::Class,
                [
                    'label'   => 'Label',
                    'choices' => [
                        'default' => 'default',
                        'primary' => 'primary',
                        'info'    => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger'  => 'danger',
                    ],
                ]
            )
            ->add(
                'posts',
                EntityType::class,
                [
                    'label'         => 'Articles',
                    'class'         => 'AppBundle:Post',
                    // 'empty_data'    => null,
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('post')
                                  ->orderBy('post.title');
                    },
                ]
            )
            ->add(
                'pages',
                EntityType::class,
                [
                    'label'         => 'Pages',
                    'class'         => 'AppBundle:Page',
                    // 'empty_data'    => null,
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('page')
                                  ->orderBy('page.title');
                    },
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Tag',
            ]
        );
    }

    public function getName()
    {
        return 'tag';
    }
}
