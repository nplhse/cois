<?php

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HospitalFilterType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', LocationType::class)
            ->add('size', SizeType::class)
            ->add('state', StateType::class)
            ->add('supplyArea', SupplyAreaType::class)
            ->add('dispatchArea', DispatchAreaType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Filter Hospitals',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
