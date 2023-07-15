<?php

declare(strict_types=1);

namespace App\Form\User;

use Domain\Contracts\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfileChangeType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var UserInterface|null $user */
        $user = $this->security->getUser();

        $builder
            ->add('fullName', TextType::class, [
                'required' => false,
                'data' => (null !== $user ? $user->getFullName() : ''),
                'constraints' => [new Length(['max' => 255])],
            ])
            ->add('biography', TextType::class, [
                'required' => false,
                'data' => (null !== $user ? $user->getBiography() : ''),
                'constraints' => [new Length(['max' => 255])],
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'data' => (null !== $user ? $user->getLocation() : ''),
                'constraints' => [new Length(['max' => 255])],
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'data' => (null !== $user ? $user->getWebsite() : ''),
        'constraints' => [new Length(['max' => 255])],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update profile',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
