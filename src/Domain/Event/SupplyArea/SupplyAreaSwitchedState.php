<?php

declare(strict_types=1);

namespace App\Domain\Event\SupplyArea;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class SupplyAreaSwitchedState extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'supply_area.switched_state';

    private SupplyAreaInterface $area;

    private StateInterface $state;

    public function __construct(SupplyAreaInterface $area, StateInterface $state)
    {
        $this->area = $area;
        $this->state = $state;
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
