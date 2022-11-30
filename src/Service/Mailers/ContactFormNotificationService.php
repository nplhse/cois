<?php

namespace App\Service\Mailers;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactFormNotificationService extends AbstractNotificationService
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly UrlGeneratorInterface $router,
        protected readonly MailerInterface $mailer,
        protected readonly UserRepository $userRepository,
        protected readonly string $appMailerSender,
        protected readonly string $appMailerFrom,
    ) {
    }

    public function sendContactFormNotification(array $contactRequest): void
    {
        foreach ($this->userRepository->findAdmins() as $admin) {
            /** @var NotificationEmail $email */
            $email = $this->getEmail(
                $admin->getEmail(),
                'notification.contact.request.title',
                'emails/notifications/contact_request.inky.twig',
                [
                    'name' => $contactRequest['name'],
                    'email_address' => $contactRequest['email_address'],
                    'subject' => $contactRequest['subject'],
                    'message' => $contactRequest['message'],
                ],
            );

            $email->importance(NotificationEmail::IMPORTANCE_HIGH);

            $this->send($email);
        }
    }
}
