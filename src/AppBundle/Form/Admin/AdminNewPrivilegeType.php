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
 * Class AdminNewPrivilegeType
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class AdminNewPrivilegeType extends AbstractType
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
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Privilege',
            ]
        );
    }

    public function getName()
    {
        return 'privilege';
    }
}
