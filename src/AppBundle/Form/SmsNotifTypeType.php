<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class SmsNotifTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam')
            ->add('omschrijving')
            ->add('ordertypes', EntityType::class,
                array(
                    'label'         => 'Linked OrderTypes',
                    'class'         => 'AppBundle:OrderType',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')->orderBy('o.beschrijving');
                    },
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => true,
                    'attr'          => array(
                        'placeholder'   => 'Selecteer de gerelateerde OrderTypes',
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
            'data_class' => 'AppBundle\Entity\SmsNotifType'
        ));
    }
}
