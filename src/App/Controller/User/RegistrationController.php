<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Domain\Command\User\RegisterUserCommand;
use Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: 'register')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private TranslatorInterface $translator,
        private bool $appEnableRegistration,
        private bool $appEnableTerms
    ) {
    }

    #[Route('/', name: 'app_register')]
    public function register(Request $request, UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordEncoder, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): ?Response
    {
        if (!$this->appEnableRegistration) {
            throw $this->createNotFoundException('Registration is currently not available.');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'enable_terms' => $this->appEnableTerms,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new RegisterUserCommand(
                $user->getUsername(),
                $user->getEmail(),
                $form->get('plainPassword')->getData()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', $this->translator->trans('flash.registration.failure'));

                return $this->render('user/registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            /** @var ?UserInterface $user */
            $user = $userRepository->findOneByUsername($user->getUsername());

            $this->addFlash('success', $this->translator->trans('flash.registration.success'));

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('user/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
