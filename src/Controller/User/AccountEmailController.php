<?php

namespace App\Controller\User;

use App\Form\EmailChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/email")
 * @IsGranted("ROLE_USER")
 */
class AccountEmailController extends AbstractController
{
    /**
     * @Route("/", name="account_email")
     */
    public function email(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EmailChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['new_email']) {
                $user->setEmail($data['new_email']);
                $user->setIsVerified(false);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your E-Mail address has been updated. Also a confirmation has been sent to your E-Mail address.');

            return $this->redirectToRoute('account_email');
        }

        return $this->render('user/email_change.html.twig', [
            'user' => $user,
            'form_email' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }
}
