<?php

declare(strict_types=1);

namespace App\EventSubscriber\EasyAdmin;

use App\Entity\Import;
use Domain\Command\Import\DeleteImportCommand;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ImportDeleteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityDeletedEvent::class => ['deleteRelatedAllocations'],
        ];
    }

    public function deleteRelatedAllocations(BeforeEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Import)) {
            return;
        }

        $command = new DeleteImportCommand($entity->getId());
        $this->messageBus->dispatch($command);
    }
}
