<?php

namespace App\Service\Mailers;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactFormMailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private TranslatorInterface $translator,
        private UserRepository $userRepository,
        private readonly string $appMailerSender,
        private readonly string $appMailerFrom,
    ) {
    }

    public function sendContactFormNotification(array $contactRequest): void
    {
        foreach ($this->userRepository->findAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->appMailerSender, $this->appMailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_HIGH)
                ->subject($this->translator->trans('notification.contact.request.title', [], 'emails'))
                ->htmlTemplate('emails/notification/contact_request.inky.twig')
                ->context([
                    'name' => $contactRequest['name'],
                    'email_address' => $contactRequest['email_address'],
                    'subject' => $contactRequest['subject'],
                    'message' => $contactRequest['message'],
                ]);

            $this->mailer->send($email);
        }
    }
}
