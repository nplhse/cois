<?php

declare(strict_types=1);

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class HospitalFilterSetType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

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
            ->add('ownHospitals', OwnHospitalFilterType::class);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('owner', HospitalOwnerFilterType::class);
        }

        $builder
            ->add('reset', ResetType::class, [
                'label' => 'Reset filters',
            ])
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
