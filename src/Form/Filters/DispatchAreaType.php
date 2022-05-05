<?php

namespace App\Form\Filters;

use App\Entity\DispatchArea;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DispatchAreaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => DispatchArea::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('s')
                ->orderBy('s.name', \Doctrine\Common\Collections\Criteria::ASC),
            'choice_label' => 'name',
            'placeholder' => 'All DispatchAreas',
            'empty_data' => '',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
