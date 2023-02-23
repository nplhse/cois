<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Domain\Enum\HospitalTier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TierType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => HospitalTier::class,
            'required' => false,
            'placeholder' => 'All tiers',
            'choice_label' => fn ($choice) => match ($choice) {
                HospitalTier::BASIC => HospitalTier::BASIC->value,
                HospitalTier::EXTENDED => HospitalTier::EXTENDED->value,
                HospitalTier::FULL => HospitalTier::FULL->value,
                default => '',
            },
            'choices' => HospitalTier::cases(),
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
