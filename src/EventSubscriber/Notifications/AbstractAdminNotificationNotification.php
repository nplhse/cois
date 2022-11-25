<?php

namespace App\EventSubscriber\Notifications;

use App\Domain\Contracts\UserInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminNotificationNotification implements AdminNotificationInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $router,
        private readonly MailerInterface $mailer,
        private readonly string $mailerSender,
        private readonly string $mailerFrom
    ) {
    }

    public function getEmail(string $recipient, string $subject, string $template, array $context = []): NotificationEmail
    {
        return (new NotificationEmail())
            ->from(new Address($this->mailerSender, $this->mailerFrom))
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
