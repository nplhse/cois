<?php

namespace App\Controller\Settings;

use App\Form\EmailChangeType;
use App\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ChangeEmailController extends AbstractController
{
    private MailerService $mailer;

    private VerifyEmailHelperInterface $verifyEmailHelper;

    private TranslatorInterface $translator;

    public function __construct(MailerService $mailer, VerifyEmailHelperInterface $verifyEmailHelper, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->translator = $translator;
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
                $this->mailer->sendVerificationEmail($user, $signatureComponents->getSignedUrl(), 3600);
            } catch (ExceptionInterface $e) {
                $this->addFlash('warning', $this->translator->trans('Failed to send verification E-Mail. Please try again later.'));

                return $this->redirectToRoute('app_settings_email');
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

    #[Route('/settings/email/verify', name: 'account_email_verify', )]
    public function sendVerification(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if ($user->getIsVerified()) {
            $this->addFlash('info', $this->translator->trans('Your E-Mail address is already verified.'));

            return $this->redirectToRoute('app_settings_email');
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_confirm_email',
            $user->getId(),
            $user->getEmail()
        );

        try {
            $this->mailer->sendVerificationEmail($user, $signatureComponents->getSignedUrl(), 3600);
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', $this->translator->trans('Failed to send verification E-Mail. Please try again later.'));

            return $this->redirectToRoute('app_settings_email');
        }

        $this->addFlash('success', $this->translator->trans('A verification E-Mail has been sent to you, please check your Inbox.'));

        return $this->redirectToRoute('app_settings_email');
    }
}
