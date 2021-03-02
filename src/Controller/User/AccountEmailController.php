<?php

namespace App\Controller\User;

use App\Form\EmailChangeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @Route("/user/email")
 * @IsGranted("ROLE_USER")
 */
class AccountEmailController extends AbstractController
{
    private MailerInterface $mailer;

    private VerifyEmailHelperInterface $verifyEmailHelper;

    public function __construct(MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;
    }

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

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_confirm_email',
                $user->getId(),
                $user->getEmail()
            );

            try {
                $email = (new TemplatedEmail())
                    ->to(new Address($user->getEmail()))
                    ->subject('Please verify your new E-Mail address')
                    ->htmlTemplate('user/emails/verify_email.html.twig')
                    ->context([
                        'signedUrl' => $signatureComponents->getSignedUrl(),
                        'expiration' => '3600',
                    ]);

                $this->mailer->send($email);
            } catch (ExceptionInterface $e) {
                $this->addFlash('warning', 'Your E-Mail address has been updated. But the system failed to send you an confirmation message.');

                return $this->redirectToRoute('account_email');
            }

            $this->addFlash('success', 'Your E-Mail address has been updated. Also a confirmation has been sent to your E-Mail address.');

            return $this->redirectToRoute('account_email');
        }

        return $this->render('user/email_change.html.twig', [
            'user' => $user,
            'form_email' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }

    /**
     * @Route("/verify", name="account_email_verify")
     */
    public function sendVerification(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Your E-Mail address is already verified.');

            return $this->redirectToRoute('account_email');
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_confirm_email',
            $user->getId(),
            $user->getEmail()
        );

        try {
            $email = (new TemplatedEmail())
                ->to(new Address($user->getEmail()))
                ->subject('Please verify your new E-Mail address')
                ->htmlTemplate('user/emails/verify_email.html.twig')
                ->context([
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiration' => '3600',
                ]);

            $this->mailer->send($email);
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', 'Cannot send you an new confirmation E-Mail.');

            return $this->redirectToRoute('account_email');
        }

        $this->addFlash('success', 'A new confirmation has been sent to your E-Mail address.');

        return $this->redirectToRoute('account_email');
    }
}
