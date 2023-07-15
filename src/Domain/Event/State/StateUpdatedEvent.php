<?php

declare(strict_types=1);

namespace Domain\Event\State;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\StateInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class StateUpdatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'state.updated';

    public function __construct(
        private StateInterface $state
    ) {
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
