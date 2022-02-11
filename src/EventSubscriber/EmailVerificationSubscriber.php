<?php

namespace App\EventSubscriber;

use App\Domain\Event\User\UserChangedEmailEvent;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class EmailVerificationSubscriber implements EventSubscriberInterface
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@example.com', 'Collaborative IVENA statistics'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('emails/user/confirmation_email.inky.twig')
        );
    }

    public function onUserChangedEmail(UserChangedEmailEvent $event): void
    {
        $user = $event->getUser();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@example.com', 'Collaborative IVENA statistics'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('emails/user/confirmation_email.inky.twig')
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisteredEvent::NAME => ['onUserRegistered', 0],
            UserChangedEmailEvent::NAME => ['onUserChangedEmail', 0],
        ];
    }
}
