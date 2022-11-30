<?php

namespace App\EventSubscriber\Notifications;

use App\Domain\Event\Hospital\HospitalCreatedEvent;

class HospitalCreatedNotification extends AbstractAdminNotification
{
    public function sendHospitalCreatedNotification(HospitalCreatedEvent $event): void
    {
        $context = $this->getContext($event);

        foreach ($this->getRecipients() as $recipient) {
            $email = $this->getEmail(
                $recipient,
                'notifications.hospital.created.title',
                'emails/notifications/hospital_created.inky.twig',
                $context,
            );

            $email->action(
                $this->getTranslation('notification.hospital.created.btn.review'),
                $this->getRoute('app_hospital_show', ['id' => $context['hospital_id']])
            );

            $this->send($email);
        }
    }

    public function getContext(object $event): array
    {
        $hospital = $event->getHospital();

        return [
            'hospital_id' => $hospital->getId(),
            'hospital_name' => $hospital->getName(),
            'hospital_owner' => $hospital->getOwner(),
            'hospital_state' => $hospital->getState(),
            'hospital_dispatchArea' => $hospital->getDispatchArea(),
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HospitalCreatedEvent::NAME => ['sendHospitalCreatedNotification', 0],
        ];
    }
}
