<?php

declare(strict_types=1);

namespace App\Domain\Event\State;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class StateDeletedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'state.deleted';

    public function __construct(
        private StateInterface $state
    ) {
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
