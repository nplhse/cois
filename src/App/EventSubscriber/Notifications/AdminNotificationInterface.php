<?php

declare(strict_types=1);

namespace App\EventSubscriber\Notifications;

use Symfony\Bridge\Twig\Mime\NotificationEmail;

interface AdminNotificationInterface
{
    public function getContext(object $event): array;

    public function getEmail(string $recipient, string $subject, string $template, array $context = []): NotificationEmail;

    public function getRecipients(): array;

    public function getRoute(string $route, array $parameters = []): string;

    public function getTranslation(string $string, array $parameters = [], string $domain = 'emails'): string;

    public static function getSubscribedEvents(): array;

    public function send(NotificationEmail $email): void;
}
