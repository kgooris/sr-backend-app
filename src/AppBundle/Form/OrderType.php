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
use Doctrine\ORM\EntityRepository;


class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	
		$builder
            ->add('smsBestelNr')
            ->add('smsDateTime', "datetime", array (
            		'data' => new \DateTime('now')
            ))
			->add('festivaldag', EntityType::class, array(
					'class' => 'AppBundle:FestivalDag',
					'label' => 'kies festivaldag'
					))
            ->add('drankstand', EntityType::class, array(
            		'class' => 'AppBundle:DrankStand',
            		'choice_label' => 'naam',))
            ->add('ordertype',  EntityType::class, array(
            		'class' => 'AppBundle:OrderType',
            		'choice_label' => 'beschrijving',))
            ->add('od', CollectionType::class, array(
            		'entry_type' => OrderDrankType::class,
            		'allow_add' => true,
            		'by_reference' => false,
            		'allow_delete' => true,
            		'error_bubbling' => false,
            		'label' => false,
            		'attr' => array(
            				'class' => 'my-selector',
            		),
            		
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
