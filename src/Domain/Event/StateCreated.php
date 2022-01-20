<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class StateCreated extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'state.created';

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
