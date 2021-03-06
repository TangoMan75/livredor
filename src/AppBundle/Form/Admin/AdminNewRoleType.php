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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminNewRoleType
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class AdminNewRoleType extends AbstractType
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
                'label',
                ChoiceType::Class,
                [
                    'label'   => 'Label',
                    'choices' => [
                        'Défaut'    => 'default',
                        'Principal' => 'primary',
                        'Info'      => 'info',
                        'Succès'    => 'success',
                        'Alerte'    => 'warning',
                        'Danger'    => 'danger',
                    ],
                ]
            )
            ->add(
                'icon',
                ChoiceType::Class,
                [
                    'label'   => 'Icône',
                    'choices' => [
                        'Utilisateur' => 'glyphicon glyphicon-user',
                        'Pion'        => 'glyphicon glyphicon-pawn',
                        'Cavalier'    => 'glyphicon glyphicon-knight',
                        'Fou'         => 'glyphicon glyphicon-bishop',
                        'Tour'        => 'glyphicon glyphicon-tower',
                        'Reine'       => 'glyphicon glyphicon-queen',
                        'Roi'         => 'glyphicon glyphicon-king',
                    ],
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
                'data_class' => 'AppBundle\Entity\Role',
            ]
        );
    }

    public function getName()
    {
        return 'role';
    }
}
