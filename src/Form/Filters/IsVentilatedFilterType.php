<?php

namespace App\Form\Filters;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IsVentilatedFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-inline checkbox-switch',
                ],
                'label' => 'Is Ventilated',
            ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
