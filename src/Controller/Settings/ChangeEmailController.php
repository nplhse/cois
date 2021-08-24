<?php

namespace App\Controller\Settings;

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
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ChangeEmailController extends AbstractController
{
    private MailerInterface $mailer;

    private VerifyEmailHelperInterface $verifyEmailHelper;

    public function __construct(MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;
    }

    #[Route('/settings/email', name: 'app_settings_email', )]
    public function email(Request $request, TranslatorInterface $translator): Response
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
                    ->subject($translator->trans('msg.verify.email'))
                    ->htmlTemplate('user/emails/verify_email.html.twig')
                    ->context([
                        'signedUrl' => $signatureComponents->getSignedUrl(),
                        'expiration' => '3600',
                    ]);

                $this->mailer->send($email);
            } catch (ExceptionInterface $e) {
                $this->addFlash('warning', $translator->trans('msg.verfiyEmail.failure'));

                return $this->redirectToRoute('app_settings_email');
            }

            $this->addFlash('success', $translator->trans('msg.verfiyEmail.success'));

            return $this->redirectToRoute('app_settings_email');
        }

        return $this->render('settings/email/index.html.twig', [
            'user' => $user,
            'form_email' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }

    /**
     * @Route("/verify", name="account_email_verify")
     */
    public function sendVerification(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if ($user->getIsVerified()) {
            $this->addFlash('warning', $translator->trans('msg.email.alreadyVerified'));

            return $this->redirectToRoute('app_settings_email');
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_confirm_email',
            $user->getId(),
            $user->getEmail()
        );

        try {
            $email = (new TemplatedEmail())
                ->to(new Address($user->getEmail()))
                ->subject($translator->trans('msg.verify.email'))
                ->htmlTemplate('user/emails/verify_email.html.twig')
                ->context([
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiration' => '3600',
                ]);

            $this->mailer->send($email);
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', $translator->trans('msg.verify.email.failed'));

            return $this->redirectToRoute('app_settings_email');
        }

        $this->addFlash('success', $translator->trans('msg.verify.email.success'));

        return $this->redirectToRoute('app_settings_email');
    }
}
