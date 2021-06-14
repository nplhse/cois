<?php

namespace App\Controller\User;

use App\Form\PasswordChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale<%app.supported_locales%>}/user/security")
 * @IsGranted("ROLE_USER")
 */
class AccountSecurityController extends AbstractController
{
    /**
     * @Route("/", name="account_security")
     */
    public function security(): Response
    {
        return $this->redirectToRoute('account_change_password');
    }

    /**
     * @Route("/change_password", name="account_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user->setPassword($passwordEncoder->encodePassword($user, $data['plainPassword']));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('msg.user.password.changed'));

            return $this->redirectToRoute('account_change_password');
        }

        return $this->render('user/password_change.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
