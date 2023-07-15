<?php

declare(strict_types=1);

namespace App\Service\Mailers;

use App\Entity\ContactRequest;
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
        protected readonly string $appMailerAddress,
    ) {
    }

    public function sendContactFormNotification(ContactRequest $contactRequest): void
    {
        foreach ($this->userRepository->findAdmins() as $admin) {
            /** @var NotificationEmail $email */
            $email = $this->getEmail(
                $admin->getEmail(),
                'notification.contact.request.title',
                'emails/notifications/contact_request.inky.twig',
                [
                    'name' => $contactRequest->getName(),
                    'email_address' => $contactRequest->getEmail(),
                    'subject' => $contactRequest->getSubject(),
                    'message' => $contactRequest->getText(),
                ],
            );

            $email->importance(NotificationEmail::IMPORTANCE_HIGH);

            $this->send($email);
        }
    }
}
