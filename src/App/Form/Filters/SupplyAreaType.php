<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Entity\SupplyArea;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplyAreaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => SupplyArea::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('s')
                ->orderBy('s.name', \Doctrine\Common\Collections\Criteria::ASC),
            'choice_label' => 'name',
            'placeholder' => 'All SupplyAreas',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}