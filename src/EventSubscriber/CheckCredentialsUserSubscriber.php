<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AccountCredentialsExpiredException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckCredentialsUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport instanceof Passport) {
            throw new \Exception('Unexpected passport type');
        }

        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        if ($user->hasCredentialsExpired()) {
            //throw new AccountCredentialsExpiredException();
        }
    }

    /**
     * @return void
     */
    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountCredentialsExpiredException) {
            return;
        }

        $response = new RedirectResponse(
            $this->router->generate('app_reset_credentials')
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -50],
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }
}
