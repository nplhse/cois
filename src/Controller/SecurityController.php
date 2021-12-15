<?php

namespace App\Controller;

use App\Form\SecurityLoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    private VerifyEmailHelperInterface $verifyEmailHelper;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
    }

    #[Route(path: 'login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // create login form

        $form = $this->createForm(
            SecurityLoginType::class,
            [
                'username' => $lastUsername,
            ]
        );

        return $this->render(
            'security/login.html.twig',
            [
                'form' => $form->createView(),
                'error' => $error,
            ]
        );
    }

    #[Route(path: 'logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: 'verify', name: 'app_confirm_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            $id = $request->get('id'); // retrieve the user id from the url

            // Verify the user id exists and is not null
            if (null === $id) {
                return $this->redirectToRoute('app_dashboard');
            }

            $user = $userRepository->findOneBy(['id' => $id]);

            // Ensure the user exists in persistence
            if (null === $user) {
                return $this->redirectToRoute('app_dashboard');
            }
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

            $user->setIsVerified(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (VerifyEmailExceptionInterface) {
            $this->addFlash('danger', $translator->trans('Your E-Mail address could not be verified.'));

            return $this->redirectToRoute('app_dashboard');
        }

        $this->addFlash('success', $translator->trans('Your E-Mail address has been successfully verified.'));

        return $this->redirectToRoute('app_dashboard');
    }
}
