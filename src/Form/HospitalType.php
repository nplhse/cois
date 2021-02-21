<?php

namespace App\Form;

use App\Entity\Hospital;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HospitalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('supplyArea')
            ->add('dispatchArea')
            ->add('size', ChoiceType::class, [
                'choices' => [
                    'large' => 'large',
                    'medium' => 'medium',
                    'small' => 'small',
                ],
            ])
            ->add('location', ChoiceType::class, [
                'choices' => [
                    'urban' => 'urban',
                    'rural' => 'rural',
                ],
            ])
            ->add('beds')
            ->add('owner')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hospital::class,
        ]);
    }
}
