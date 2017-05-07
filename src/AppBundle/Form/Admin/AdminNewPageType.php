<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class AdminNewPageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::Class,
                [
                    'label' => 'Titre',
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'label' => 'Étiquette',
                    'class' => 'AppBundle:Tag',
//                    'empty_data' => null,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('tag')
                            ->join('tag.items', 'items');
                    },
                ]
            )
            ->add(
                'sections',
                EntityType::class,
                [
                    'label' => 'Sections',
                    'class' => 'AppBundle:Section',
//                    'empty_data' => null,
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('section')
                            ->orderBy('section.title');
                    },
                ]
            )
            ->add(
                'published',
                CheckboxType::class,
                [
                    'label'    => 'Publier',
                    'required' => false,
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
                'data_class' => 'AppBundle\Entity\Page',
            ]
        );
    }

    public function getName()
    {
        return 'page';
    }
}
