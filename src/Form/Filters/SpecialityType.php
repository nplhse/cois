<?php

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialityType extends AbstractType
{
    private AllocationRepository $allocationRepository;

    public function __construct(AllocationRepository $allocationRepository)
    {
        $this->allocationRepository = $allocationRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => array_flip($this->allocationRepository->getSpecialityArray()),
            'placeholder' => 'All specialities',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}