<?php

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class IndicationType extends AbstractType
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
            'choices' => array_flip($this->getIndications()),
            'placeholder' => 'All indications',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getIndications(): array
    {
        return $this->cache->get('form-indications', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['form', 'form-indications']);

            return $this->allocationRepository->getIndicationsArray();
        });
    }
}
