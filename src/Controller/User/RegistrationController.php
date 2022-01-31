<?php

namespace App\Controller\User;

use App\Domain\Command\User\RegisterUserCommand;
use App\Domain\Repository\UserRepositoryInterface;
use App\Entity\User;
use App\Form\User\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\AdminNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route(path: 'register')]
class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    private AdminNotificationService $adminNotifier;

    private EntityManagerInterface $entityManager;

    private MessageBusInterface $messageBus;

    public function __construct(EmailVerifier $emailVerifier, AdminNotificationService $adminNotifier, EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->emailVerifier = $emailVerifier;
        $this->adminNotifier = $adminNotifier;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_register')]
    public function register(Request $request, UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordEncoder, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): ?Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new RegisterUserCommand(
                $user->getUsername(),
                $user->getEmail(),
                $form->get('plainPassword')->getData()
            );

            //try {
            $this->messageBus->dispatch($command);
            //} catch (HandlerFailedException $e) {
            //    $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            //}

            $user = $userRepository->findOneByUsername($user->getUsername());

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

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
