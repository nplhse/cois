<?php

declare(strict_types=1);

namespace App\EventSubscriber\Notifications;

use Domain\Contracts\UserInterface;
use Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminNotification implements AdminNotificationInterface, EventSubscriberInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $router,
        private readonly MailerInterface $mailer,
        private readonly string $appMailerSender,
        private readonly string $appMailerAddress
    ) {
    }

    public function getEmail(string $recipient, string $subject, string $template, array $context = []): NotificationEmail
    {
        return (new NotificationEmail())
            ->from(new Address($this->appMailerAddress, $this->appMailerSender))
            ->to(new Address($recipient))
            ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
            ->subject($this->getTranslation($subject))
            ->htmlTemplate($template)
            ->context($context);
    }

    public function getRecipients(): array
    {
        $recipients = [];

        /** @var UserInterface $admin */
        foreach ($this->userRepository->findAdmins() as $admin) {
            $recipients[] = $admin->getEmail();
        }

        return $recipients;
    }

    public function getRoute(string $route, array $parameters = []): string
    {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getTranslation(string $string, array $parameters = [], string $domain = 'emails'): string
    {
        return $this->translator->trans($string, $parameters, $domain);
    }

    abstract public function getContext(object $event): array;

    abstract public static function getSubscribedEvents(): array;

    /**
     * @throws TransportExceptionInterface
     */
    public function send(NotificationEmail $email): void
    {
        $this->mailer->send($email);
    }
}
