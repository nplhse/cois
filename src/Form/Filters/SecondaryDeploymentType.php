<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Repository\AllocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SecondaryDeploymentType extends AbstractType
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
            'choices' => $this->getSecondaryDeployments(),
            'placeholder' => 'Secondary Deployments',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getSecondaryDeployments(): array
    {
        return $this->cache->get('form-sec-deployments', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $item->tag(['form', 'form-sec-deployments']);

            return $this->allocationRepository->getSecondaryDeployments();
        });
    }
}
