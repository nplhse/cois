<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Domain\Enum\HospitalLocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => HospitalLocation::class,
            'required' => false,
            'placeholder' => 'All locations',
            'choice_label' => fn (mixed $choice) => match ($choice) {
                HospitalLocation::URBAN => HospitalLocation::URBAN->value,
                HospitalLocation::RURAL => HospitalLocation::RURAL->value,
                default => '',
            },
            'choices' => HospitalLocation::cases(),
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
