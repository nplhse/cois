<?php

declare(strict_types=1);

namespace App\Domain\Event\DispatchArea;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaSwitchedStateEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'dispatch_area.switched_state';

    public function __construct(
        private DispatchAreaInterface $area,
        private StateInterface $state
    ) {
    }

    public function getDispatchArea(): DispatchAreaInterface
    {
        return $this->area;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
