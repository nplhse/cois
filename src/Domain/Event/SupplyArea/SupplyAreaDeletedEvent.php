<?php

declare(strict_types=1);

namespace Domain\Event\SupplyArea;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class SupplyAreaDeletedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'supply_area.deleted';

    public function __construct(
        private SupplyAreaInterface $area
    ) {
    }

    public function getDispatchArea(): SupplyAreaInterface
    {
        return $this->area;
    }
}
