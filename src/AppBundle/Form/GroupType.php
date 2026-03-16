<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class GroupType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                array(
                    'required' => true,
                    'label'     => 'Groep Naam'
                ))
            ->add('rolesCollection', EntityType::class,
                array(
                    'label'         => 'Roles',
                    'class'         => 'AppBundle:Role',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')->orderBy('r.role','ASC');
                    },
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => true,
                    'attr'          => array(
                        'placeholder'   => 'RolePlaceholder',
                        'class'         => 'js-advanced-select form-control advanced-select'
                    )
                ))
           ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Group'
        ));
    }
}
