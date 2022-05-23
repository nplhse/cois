<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class MailerService
{
    private MailerInterface $mailer;

    private RouterInterface $router;

    private string $mailerSender;

    private string $mailerFrom;

    public function __construct(MailerInterface $mailer, RouterInterface $router, string $appMailerSender, string $appMailerFrom)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->mailerSender = $appMailerSender;
        $this->mailerFrom = $appMailerFrom;
    }

    public function sendImportReminderEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject('Monthly reminder to import new data')
            ->htmlTemplate('emails/import/reminder.inky.twig')
            ->context([
                'user' => $user,
                'link' => $this->router->generate('app_import_new', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(User $user, ResetPasswordToken $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject('Reset your Password')
            ->htmlTemplate('emails/user/reset_password.inky.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->mailer->send($email);
    }

    public function sendVerificationEmail(User $user, string $signedUrl, int $expiration): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject('Verify your E-Mail address')
            ->htmlTemplate('emails/user/verify_email.inky.twig')
            ->context([
                'user' => $user,
                'signedUrl' => $signedUrl,
                'expiration' => '3600',
            ]);

        $this->mailer->send($email);
    }

    public function sendWelcomeEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->from(new Address($this->mailerSender, $this->mailerFrom))
            ->replyTo($this->mailerSender)
            ->subject('Welcome to Collaborative IVENA statistics')
            ->htmlTemplate('emails/user/welcome.twig')
            ->context([
                'user' => $user,
                'link' => $this->router->generate('app_login', [], UrlGenerator::ABSOLUTE_URL),
            ]);

        $this->mailer->send($email);
    }
}
