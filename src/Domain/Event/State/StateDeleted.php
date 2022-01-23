<?php

declare(strict_types=1);

namespace App\Domain\Event\State;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class StateDeleted extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'state.deleted';

    private StateInterface $state;

    public function __construct(StateInterface $state)
    {
        $this->state = $state;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
