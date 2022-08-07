<?php

namespace App\Form;

use App\Domain\Contracts\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ContactType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var UserInterface|null $user */
        $user = $this->security->getUser();

        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => true,
                'data' => (null !== $user ? $user->getUserIdentifier() : ''),
                ])
            ->add('email_address', EmailType::class, [
                'label' => 'label.email',
                'required' => true,
                'data' => (null !== $user ? $user->getEmail() : ''),
            ])
            ->add('subject', TextType::class, [
                'label' => 'label.subject',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'label.message',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'label.submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'forms',
        ]);
    }
}
