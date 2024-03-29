<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Security\EmailVerifier;
use Domain\Event\User\UserChangedEmailEvent;
use Domain\Event\User\UserRegisteredEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailVerificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator,
        private string $appMailerSender,
        private string $appMailerAddress,
    ) {
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->appMailerAddress, $this->appMailerSender))
                ->to(new Address($user->getEmail()))
                ->subject($this->translator->trans('confirm.email.title', [], 'emails'))
                ->htmlTemplate('emails/user/confirmation_email.html.twig')
        );
    }

    public function onUserChangedEmail(UserChangedEmailEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->appMailerAddress, $this->appMailerSender))
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
