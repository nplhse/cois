<?php

namespace App\Service;

use App\Domain\Contracts\UserInterface;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class MailerService
{
    private MailerInterface $mailer;

    private RouterInterface $router;

    private TranslatorInterface $translator;

    private string $mailerSender;

    private string $mailerFrom;

    public function __construct(MailerInterface $mailer, RouterInterface $router, TranslatorInterface $translator, string $appMailerSender, string $appMailerFrom)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->translator = $translator;
        $this->mailerSender = $appMailerSender;
        $this->mailerFrom = $appMailerFrom;
    }

    public function sendImportReminderEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject($this->translator->trans('import.reminder.title', [], 'emails'))
            ->htmlTemplate('emails/import/reminder.html.twig')
            ->context([
                'username' => $user->getUsername(),
                'targetUrl' => $this->router->generate('app_import_new', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(User $user, ResetPasswordToken $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject($this->translator->trans('account.reset.title', [], 'emails'))
            ->htmlTemplate('emails/user/reset_password.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'expiration' => '3600',
            ]);

        $this->mailer->send($email);
    }

    public function sendVerificationEmail(User $user, string $signedUrl, int $expiration): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject($this->translator->trans('account.verify.title', [], 'emails'))
            ->htmlTemplate('emails/user/verify_email.html.twig')
            ->context([
                'user' => $user,
                'signedUrl' => $signedUrl,
                'expiration' => '3600',
            ]);

        $this->mailer->send($email);
    }

    public function sendPromotionEmail(UserInterface $user): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject($this->translator->trans('account.promoted.title', [], 'emails'))
            ->htmlTemplate('emails/user/promoted.html.twig')
            ->context([
                'user' => $user,
                'targetUrl' => $this->router->generate('app_dashboard', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }

    public function sendWelcomeEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject($this->translator->trans('account.welcome.title', [], 'emails'))
            ->htmlTemplate('emails/user/welcome.html.twig')
            ->context([
                'username' => $user->getUsername(),
                'targetUrl' => $this->router->generate('app_login', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }
}
