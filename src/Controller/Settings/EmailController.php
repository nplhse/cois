<?php

declare(strict_types=1);

namespace App\Controller\Settings;

use App\Domain\Command\User\ChangeEmailCommand;
use App\Entity\User;
use App\Form\User\EmailChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class EmailController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private TranslatorInterface $translator
    ) {
    }

    #[Route('/settings/email', name: 'app_settings_email', )]
    public function email(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(EmailChangeType::class);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('new_email')->getData()) {
                $command = new ChangeEmailCommand($user->getId(), $form->get('new_email')->getData());

                try {
                    $this->messageBus->dispatch($command);
                } catch (HandlerFailedException) {
                    $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
                }
            }

            $this->addFlash('success', $this->translator->trans('A verification E-Mail has been sent to you, please check your Inbox.'));

            return $this->redirectToRoute('app_settings_email');
        }

        return $this->render('settings/email/index.html.twig', [
            'user' => $user,
            'form_email' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
