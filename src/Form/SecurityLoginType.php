<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SecurityLoginType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<mixed>                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
        ;

        if ($this->security->isGranted('IS_REMEMBERED')) {
            /** @var User $user */
            $user = $this->security->getUser();

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
                $form = $event->getForm();

                $form->remove('username');
                $form->add('username', HiddenType::class, [
                    'data' => $user->getUsername(),
                ]);
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
