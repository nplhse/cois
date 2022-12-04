<?php

declare(strict_types=1);

namespace App\EventSubscriber\Notifications;

use App\Domain\Event\User\UserRegisteredEvent;

class UserCreatedNotification extends AbstractAdminNotification
{
    public function sendUserRegisteredNotification(UserRegisteredEvent $event): void
    {
        $context = $this->getContext($event);

        foreach ($this->getRecipients() as $recipient) {
            $email = $this->getEmail(
                $recipient,
                'notification.user.new.title',
                'emails/notifications/user_registered.inky.twig',
                $context,
            );

            $this->send($email);
        }
    }

    public function getContext(object $event): array
    {
        $user = $event->getUser();

        return [
            'user_id' => $user->getId(),
            'user_name' => $user->getUsername(),
            'user_email' => $user->getEmail(),
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::NAME => ['sendUserRegisteredNotification', 0],
        ];
    }
}
