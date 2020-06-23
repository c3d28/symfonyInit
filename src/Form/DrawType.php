<?php

namespace App\Form;

use App\Entity\Draw;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class DrawType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            //->add('dateCreation')
            ->add('dateDraw',
                DateTimeType::class,
                [
                    'date_format' => 'dd-MM-yyyy HH:mm',
                    'attr' => [
                                'id' => 'testced'
                            ]
                ]
            )
            ->add('type',
                ChoiceType::class,
                [
                    'choices' => [
                        'Unique' => 'unique',
                        'Tirage de tous les participants' => 'all_participant',
                        'Tirage de tous les lots' => 'all_gift'
                    ]
                ]
            )

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Draw::class
        ]);
    }
}
