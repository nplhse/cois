<?php

declare(strict_types=1);

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IsPregnantFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch',
                ],
                'label' => 'Is Pregnant',
            ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
