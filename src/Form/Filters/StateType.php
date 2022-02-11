<?php

namespace App\Form\Filters;

use App\Domain\Entity\Hospital;
use App\Entity\State;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => State::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->orderBy('s.name', 'ASC');
            },
            'choice_label' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
