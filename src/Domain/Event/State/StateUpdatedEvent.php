<?php

declare(strict_types=1);

namespace App\Domain\Event\State;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\NamedEventTrait;
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
