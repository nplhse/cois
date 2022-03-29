<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserCredentialsSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private TokenStorageInterface $security;

    private RouterInterface $router;

    private array $excludedRoutes = [
        'app_logout',
        'app_verify_email',
        'app_reset_credentials',
    ];

    public function __construct(TokenStorageInterface $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
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

        $currentRoute = $event->getRequest()->attributes->get('_route');
        if (\in_array($currentRoute, $this->excludedRoutes, true)) {
            return;
        }

        $token = $this->security->getToken();

        if (null === $token) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof User && $user->hasCredentialsExpired()) {
            $response = new RedirectResponse($this->router->generate('app_reset_credentials'));
            $event->setResponse($response);
        }
    }
}
