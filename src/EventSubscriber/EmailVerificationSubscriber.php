<?php

namespace App\EventSubscriber;

use App\Domain\Event\User\UserChangedEmailEvent;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailVerificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator,
        private string $mailerSender,
        private string $mailerFrom
    ) {
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($user->getEmail()))
                ->subject($this->translator->trans('confirm.email.title', [], 'emails'))
                ->htmlTemplate('emails/user/confirmation_email.html.twig')
        );
    }

    public function onUserChangedEmail(UserChangedEmailEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($user->getEmail()))
                ->subject($this->translator->trans('confirm.email.title', [], 'emails'))
                ->htmlTemplate('emails/user/verify_email.html.twig')
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::NAME => ['onUserRegistered', 0],
            UserChangedEmailEvent::NAME => ['onUserChangedEmail', 0],
        ];
    }
}
