<?php

// src/EventSubscriber/EasyAdminSubscriber.php

namespace App\EventSubscriber\EasyAdmin;

use App\Domain\Command\Import\DeleteImportCommand;
use App\Entity\Import;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ImportDeleteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityDeletedEvent::class => ['deleteRelatedAllocations'],
        ];
    }

    public function deleteRelatedAllocations(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Import)) {
            return;
        }

        $command = new DeleteImportCommand($entity->getId());
        $this->messageBus->dispatch($command);
    }
}
