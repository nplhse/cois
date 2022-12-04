<?php

declare(strict_types=1);

namespace App\Controller\Settings;

use App\Domain\Command\User\ChangePasswordCommand;
use App\Entity\User;
use App\Form\User\ChangePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

#[IsGranted('ROLE_USER')]
class PasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route('/settings/password', name: 'app_settings_password', )]
    public function changePassword(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new ChangePasswordCommand($user->getId(), $form->get('plainPassword')->getData());

            try {
                $this->messageBus->dispatch($command);
                $this->cleanSessionAfterReset();
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->redirectToRoute('app_settings_password');
            }

            $this->addFlash('success', 'Your password has been changed successfully.');

            return $this->redirectToRoute('app_settings_password');
        }

        return $this->render('settings/password/index.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
