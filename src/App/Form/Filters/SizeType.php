<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Domain\Enum\HospitalSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SizeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => HospitalSize::class,
            'required' => false,
            'placeholder' => 'All sizes',
            'choice_label' => fn ($choice) => match ($choice) {
                HospitalSize::SMALL => HospitalSize::SMALL->value,
                HospitalSize::MEDIUM => HospitalSize::MEDIUM->value,
                HospitalSize::LARGE => HospitalSize::LARGE->value,
                default => '',
            },
            'choices' => HospitalSize::cases(),
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
