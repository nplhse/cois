<?php

declare(strict_types=1);

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrgencyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => [
                'SK1' => '1',
                'SK2' => '2',
                'SK3' => '3',
            ],
            'placeholder' => 'All urgencies',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
