<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Current password',
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 4096,
                        'groups' => ['Password_Length'],
                    ]),
                    new NotBlank([
                        'groups' => ['Password_Blank'],
                    ]),
                    new UserPassword([
                        'groups' => ['Password_Change'],
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'New password',
                ],
                'second_options' => [
                    'label' => 'Repeat new password',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Change password',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            /*
             * GroupSequence will validate constraints sequentially by iterating through the array, it means that if
             * password length validation fails, length error will be shown and validation will stop there.
             * UserPassword validation will not be triggered, thus preventing potential server load (or even DoS?)
             * if a very long password is being hashed.
             */
            'validation_groups' => new GroupSequence([
                'Password_Length',
                'Password_Blank',
                'Password_Change',
            ]),
        ]);
    }
}
