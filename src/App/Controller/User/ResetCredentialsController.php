<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\ChangePasswordFormType;
use Domain\Command\User\ChangePasswordCommand;
use Domain\Contracts\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

#[IsGranted('ROLE_USER')]
class ResetCredentialsController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    #[Route(path: '/reset-credentials', name: 'app_reset_credentials')]
    public function resetCredentials(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $user */
            $user = $this->getUser();

            $command = new ChangePasswordCommand($user->getId(), $form->get('plainPassword')->getData());

            try {
                $this->messageBus->dispatch($command);
                $this->cleanSessionAfterReset();
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            $this->addFlash('success', 'Your password has been changed successfully.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('user/reset_credentials.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
