<?php

declare(strict_types=1);

namespace App\Application\Traits;

use App\Domain\Contracts\DomainEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Adds the possibility to dispatch Events just by using this trait.
 */
trait EventDispatcherTrait
{
    private EventDispatcherInterface $eventDispatcher;

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchEvent(DomainEventInterface $event): void
    {
        /* @var DomainEventInterface $event */
        $this->eventDispatcher->dispatch($event, $event->getName());
    }
}
