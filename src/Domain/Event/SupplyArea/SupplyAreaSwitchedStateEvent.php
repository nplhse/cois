<?php

declare(strict_types=1);

namespace Domain\Event\SupplyArea;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class SupplyAreaSwitchedStateEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'supply_area.switched_state';

    public function __construct(
        private SupplyAreaInterface $area,
        private StateInterface $state
    ) {
    }

    public function getSupplyArea(): SupplyAreaInterface
    {
        return $this->area;
    }

    public function getNewState(): StateInterface
    {
        return $this->state;
    }
}
