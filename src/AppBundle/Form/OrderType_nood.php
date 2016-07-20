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


class OrderType_nood extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('festivaldag', EntityType::class, array(
                'class' => 'AppBundle:FestivalDag',
                'label' => 'Kies festivaldag'
            ))
            ->add('drankstand', EntityType::class, array(
                'class' => 'AppBundle:DrankStand',
                'choice_label' => 'naam',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ds')
                        ->where("ds.smscode <> :mainstockcode")
                        ->setParameter('mainstockcode', '11111111')
                        ->orderBy('ds.naam', 'ASC');
                },))
            ->add('ordernotes', null, array(
                'attr' =>
                    array(
                        'placeholder' => 'vb. informatie met betrekking tot de nood oproep'

                    ),
                'label' => 'NOOD Informatie',
                'required' => true

            ));
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
