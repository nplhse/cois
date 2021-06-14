<?php

namespace App\Controller\User;

use App\Form\ProfileChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale<%app.supported_locales%>}/user/profile")
 * @IsGranted("ROLE_USER")
 */
class AccountProfileController extends AbstractController
{
    /**
     * @Route("/", name="account_profile")
     */
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('msg.user.profile.updated'));

            return $this->redirectToRoute('account_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form_profile' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
