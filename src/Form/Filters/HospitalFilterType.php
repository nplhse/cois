<?php

namespace App\Form\Filters;

use App\Entity\Hospital;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HospitalFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => Hospital::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('h')
                ->orderBy('h.name', 'ASC'),
            'choice_label' => 'name',
            'empty_data' => '',
            'placeholder' => 'All hospitals',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
