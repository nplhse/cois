<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Service\Mailers\UserVerificationMailerService;
use Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyUserController extends AbstractController
{
    public function __construct(private VerifyEmailHelperInterface $verifyEmailHelper, private UserVerificationMailerService $mailer, private UserRepositoryInterface $userRepository)
    {
    }

    #[Route(path: 'verify', name: 'app_verify_email')]
    public function verifyEmail(Request $request, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        if (null === $user) {
            $id = $request->query->get('id'); // retrieve the user id from the url

            // Verify the user id exists and is not null
            if (null === $id) {
                return $this->redirectToRoute('app_dashboard');
            }

            $user = $this->userRepository->findOneByUsername($id);

            // Ensure the user exists in persistence
            if (null === $user) {
                return $this->redirectToRoute('app_dashboard');
            }
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getUserIdentifier(), $user->getEmail());

            $user->verify();
            $this->userRepository->save();
        } catch (VerifyEmailExceptionInterface) {
            $this->addFlash('danger', $translator->trans('Your E-Mail address could not be verified.'));

            return $this->redirectToRoute('app_dashboard');
        }

        $this->addFlash('success', $translator->trans('Your E-Mail address has been successfully verified.'));

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('send-verification', name: 'account_email_verify', )]
    public function sendVerification(TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();

        if ($user->isVerified()) {
            $this->addFlash('info', 'Your E-Mail address is already verified.');

            return $this->redirectToRoute('app_settings_email');
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getUsername(),
            $user->getEmail()
        );

        try {
            $this->mailer->sendVerificationEmail($user, $signatureComponents->getSignedUrl(), 3600);
        } catch (ExceptionInterface) {
            $this->addFlash('danger', 'Failed to send verification E-Mail. Please try again later.');

            return $this->redirectToRoute('app_settings_email');
        }

        $this->addFlash('success', 'A verification E-Mail has been sent to you, please check your Inbox.');

        return $this->redirectToRoute('app_settings_email');
    }
}
