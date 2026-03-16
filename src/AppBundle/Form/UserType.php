<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;



class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gsm_app', 'tel', array('default_region' => 'BE', 'format' => PhoneNumberFormat::NATIONAL))
            ->add('gsm_perso', 'tel', array('default_region' => 'BE', 'format' => PhoneNumberFormat::NATIONAL))
  	        ->add('expired')
  	        ->add('enabled')
            ->add('manager', CheckboxType::class,
                array(
                    'label'   => 'Ontvang SMS kopies',
                    'required' => false                )
            )
            ->add('groups', EntityType::class,
                array(
                    'label'         => 'Groups',
                    'class'         => 'AppBundle:Group',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('g')->orderBy('g.name','ASC');
                    },
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => true,
                    'attr'          => array(
                        'placeholder'   => 'GroupPlaceholder',
                        'class'         => 'js-advanced-select form-control advanced-select'
                    )
                ))
            ->add('smsnotifys', EntityType::class,
                array(
                    'label'         => 'Informatie opvolgen via GSMAPP over',
                    'class'         => 'AppBundle:SmsNotifType',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.naam');
                    },
                    'multiple'      => true,
                    'expanded'      => true,
                    'required'      => true,
                    'attr'          => array(
                        'placeholder'   => 'Selecteer op welke flow je je wil inschrijven',

                    )
                ))

  	       
            
        ;
    }
    public function getParent()
    {
    	return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    
    	// Or for Symfony < 2.8
    	// return 'fos_user_registration';
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
