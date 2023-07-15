<?php

declare(strict_types=1);

namespace App\EventSubscriber\Notifications;

use Domain\Event\Import\ImportFailedEvent;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class ImportFailedNotification extends AbstractAdminNotification
{
    public function sendImportFailedNotification(ImportFailedEvent $event): void
    {
        $context = $this->getContext($event);

        foreach ($this->getRecipients() as $recipient) {
            $email = $this->getEmail(
                $recipient,
                'notification.import.failed.title',
                'emails/notifications/import_failed.inky.twig',
                $context,
            );

            $email->importance(NotificationEmail::IMPORTANCE_HIGH);
            $email->action(
                $this->getTranslation('notification.import.failed.btn.review'),
                $this->getRoute('app_import_show', ['id' => $context['import_id']])
            );

            $this->send($email);
        }
    }

    public function getContext(object $event): array
    {
        $import = $event->getImport();
        $exception = $event->getException();

        return [
            'import_id' => $import->getId(),
            'import_name' => $import->getName(),
            'import_user' => $import->getUser(),
            'import_hospital' => $import->getHospital(),
            'exception' => $exception,
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ImportFailedEvent::NAME => ['sendImportFailedNotification', 0],
        ];
    }
}
