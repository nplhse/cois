<?php

declare(strict_types=1);

namespace App\Form\Filters;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => User::class,
            'choice_label' => 'username',
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('u')
                ->orderBy('u.username', \Doctrine\Common\Collections\Criteria::ASC),
            'empty_data' => '',
            'placeholder' => 'All users',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
