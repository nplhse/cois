<?php

namespace App\Controller\Settings\User;

use App\Domain\Command\User\ChangeUsernameCommand;
use App\Entity\User;
use App\Form\User\ProfileChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/settings/profile', name: 'app_settings_profile', )]
    public function profile(Request $request, TranslatorInterface $translator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new ChangeUsernameCommand($user->getId(), $form->get('username')->getData());

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->redirectToRoute('app_settings_profile');
            }

            $this->addFlash('success', $translator->trans('msg.user.profile.updated'));

            return $this->redirectToRoute('app_settings_profile');
        }

        return $this->render('settings/profile/index.html.twig', [
            'user' => $user,
            'form_profile' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
