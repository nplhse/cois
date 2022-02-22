<?php

namespace App\Form\Filters;

use App\Domain\Entity\Hospital;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SizeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => [
                'All sizes' => '',
                'small' => Hospital::SIZE_SMALL,
                'medium' => Hospital::SIZE_MEDIUM,
                'large' => Hospital::SIZE_LARGE,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
