<?php

namespace App\Controller\User;

use App\Domain\Command\User\RegisterUserCommand;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use App\Form\User\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route(path: 'register')]
class RegistrationController extends AbstractController
{
    public function __construct(private MessageBusInterface $messageBus, private bool $appRegistration, private bool $appTerms)
    {
    }

    #[Route('/', name: 'app_register')]
    public function register(Request $request, UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordEncoder, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): ?Response
    {
        if (!$this->appRegistration) {
            throw $this->createNotFoundException('Registration is currently not available.');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'enable_terms' => $this->appTerms,
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
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            /** @var ?UserInterface $user */
            $user = $userRepository->findOneByUsername($user->getUsername());

            if (null === $user) {
                throw new \RuntimeException('Sorry, something went wrong. Please try again later.');
            }

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
