<?php

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SpecialityDetailType extends AbstractType
{
    public function __construct(
        private AllocationRepository $allocationRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => array_flip($this->getSpecialityDetails()),
            'placeholder' => 'All speciality details',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getSpecialityDetails(): array
    {
        return $this->cache->get('form-speciality-details', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['form', 'form-speciality-details']);

            return $this->allocationRepository->getOccasionsArray();
        });
    }
}
