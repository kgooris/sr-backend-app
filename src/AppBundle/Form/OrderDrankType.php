<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\EntityRepository;

class OrderDrankType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hoeveel', IntegerType::class)
            ->add('drank', EntityType::class, array(
            		'class' => 'AppBundle:DrankSoort',
                    'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')->orderBy('g.rapportvolgorde','ASC');
                    },
                    'placeholder' => 'Selecteer een drank',
            ))
            
  
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrderDrank'
        ));
    }
}
