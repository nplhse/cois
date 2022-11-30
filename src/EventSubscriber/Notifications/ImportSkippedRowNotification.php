<?php

namespace App\EventSubscriber\Notifications;

use App\Domain\Event\Import\ImportSkippedRowEvent;

class ImportSkippedRowNotification extends AbstractAdminNotification
{
    public function sendImportSkippedRowNotification(ImportSkippedRowEvent $event): void
    {
        $context = $this->getContext($event);

        foreach ($this->getRecipients() as $recipient) {
            $email = $this->getEmail(
                $recipient,
                'notification.import.skipped.title',
                'emails/notifications/import_skipped.inky.twig',
                $context,
            );

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
            ImportSkippedRowEvent::NAME => ['sendImportSkippedRowNotification', 0],
        ];
    }
}
