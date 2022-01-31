<?php

namespace App\Controller\User;

use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyUserController extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    private VerifyEmailHelperInterface $verifyEmailHelper;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->verifyEmailHelper = $verifyEmailHelper;
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
}
