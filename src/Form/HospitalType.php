<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Hospital;
use App\Form\Filters\DispatchAreaType;
use App\Form\Filters\LocationType;
use App\Form\Filters\SizeType;
use App\Form\Filters\StateType;
use App\Form\Filters\SupplyAreaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HospitalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('state', StateType::class, [
                'required' => true,
            ])
            ->add('supplyArea', SupplyAreaType::class)
            ->add('dispatchArea', DispatchAreaType::class, [
                'required' => true,
            ])
            ->add('size', SizeType::class, [
                'required' => true,
            ])
            ->add('location', LocationType::class, [
                'required' => true,
            ])
            ->add('beds')
        ;

        if ($options['backend']) {
            $builder
                ->add('owner')
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hospital::class,
            'backend' => false,
        ]);
    }
}
