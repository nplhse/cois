<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateType extends UserType
{
    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation' => ['Default', 'Create'],
        ]);
    }
}
