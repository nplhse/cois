<?php

namespace App\Service;

use App\Entity\Import;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class AdminNotificationService
{
    private MailerInterface $mailer;

    private UserRepository $userRepository;

    private string $mailerSender;

    private string $mailerFrom;

    private array $admins;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository, string $mailerSender, string $mailerFrom)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->mailerSender = $mailerSender;
        $this->mailerFrom = $mailerFrom;

        $this->admins = $this->userRepository->findAdmins();
    }

    public function sendFailedImportNotification(Import $context): void
    {
        foreach ($this->admins as $user) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($user->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_HIGH)
                ->subject('A new Import has failed')
                ->htmlTemplate('emails/notification/import_failed.inky.twig')
                ->context(['import' => $context]);

            $this->mailer->send($email);
        }
    }
}
