<?php

declare(strict_types=1);

namespace App\EventSubscriber\EasyAdmin;

use App\Domain\Contracts\UserInterface;
use App\Entity\ContactRequest;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setDetails'],
            BeforeEntityUpdatedEvent::class => ['updateDetails'],
        ];
    }

    public function setDetails(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof ContactRequest)) {
            return;
        }

        $entity->setCreatedAt(new \DateTimeImmutable());
    }

    public function updateDetails(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof ContactRequest)) {
            return;
        }

        /** @var UserInterface $user */
        $user = $this->security->getUser();
        $entity->setUpdatedBy($user);

        $entity->updateTimestamps();
    }
}
