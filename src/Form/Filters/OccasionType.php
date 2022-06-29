<?php

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class OccasionType extends AbstractType
{
    private TagAwareCacheInterface $cache;

    private AllocationRepository $allocationRepository;

    public function __construct(AllocationRepository $allocationRepository, TagAwareCacheInterface $appCache)
    {
        $this->allocationRepository = $allocationRepository;
        $this->cache = $appCache;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => $this->getOccasions(),
            'placeholder' => 'All occasions',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getOccasions(): array
    {
        return $this->cache->get('form-occasions', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['form', 'form-occasions']);

            return $this->allocationRepository->getOccasionsArray();
        });
    }
}
