<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Domain\Contracts\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsernameChangeType extends AbstractType
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
            ->add('username', TextType::class, [
                'required' => true,
                'data' => (null !== $user ? $user->getUsername() : ''),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update username',
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
