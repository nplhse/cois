<?php

namespace App\Form\Filters;

use App\Domain\Entity\Hospital;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => [
                'All Locations' => '',
                'urban' => Hospital::LOCATION_URBAN,
                'rural' => Hospital::LOCATION_RURAL,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
