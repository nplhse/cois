<?php

namespace App\Controller\Settings\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Form\ProfileChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/settings/profile', name: 'app_settings_profile', )]
    public function profile(Request $request, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['username']) {
                $user->setUsername($data['username']);
            }

            $this->userRepository->save();

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
