<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AssignmentType extends AbstractType
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
            'choices' => $this->getAssignments(),
            'placeholder' => 'All assignments',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getAssignments(): array
    {
        return $this->cache->get('form-assignment', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['form', 'form-assignment']);

            return $this->allocationRepository->getAssignmentsArray();
        });
    }
}
