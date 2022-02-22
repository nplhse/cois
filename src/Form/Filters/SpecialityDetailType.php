<?php

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialityDetailType extends AbstractType
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
            'choices' => array_flip($this->allocationRepository->getSpecialityDetailArray()),
            'placeholder' => 'All speciality details',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
