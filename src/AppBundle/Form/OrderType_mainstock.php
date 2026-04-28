<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\OrderDrankType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class OrderType_mainstock extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('ordernotes', null, array(
        			'attr' => 
        				array(
        						'placeholder' => 'vb. Leveringbon nummer of andere referenties'
        						
        				),
        			'label' => 'Leveringbon Informatie',
        			'required' => true
        			
        	))
            ->add('od', CollectionType::class, array(
            		'entry_type' => OrderDrankType::class,
            		'allow_add' => true,
            		'by_reference' => false,
            		'allow_delete' => true,
            		'error_bubbling' => false,
            		'label' => false
            		
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Order'
        ));
    }
}
