<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserTermsSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $security;

    public function __construct(TokenStorageInterface $security)
    {
        $this->security = $security;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [\Symfony\Component\HttpKernel\KernelEvents::REQUEST => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $token = $this->security->getToken();

        if (null === $token) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof User && false === $user->hasAcceptedTerms()) {
            // $response = new RedirectResponse($this->router->generate('app_reset_accept_terms'));
            // $event->setResponse($response);
        }
    }
}
