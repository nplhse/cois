<?php

namespace App\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Form\ChangePasswordFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

#[IsGranted('ROLE_USER')]
class ResetCredentialsController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route(path: '/reset-credentials', name: 'app_reset_credentials')]
    public function resetCredentials(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            throw new AccessDeniedHttpException('Cannot reset Credentials if not logged in.');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);

            $this->userRepository->save();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Your password has been changed successfully.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('user/reset_credentials.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
