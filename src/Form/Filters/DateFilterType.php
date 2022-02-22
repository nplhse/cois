<?php

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'widget' => 'single_text',
        ]);
    }

    public function getParent(): string
    {
        return DateType::class;
    }
}
