<?php

declare(strict_types=1);

namespace App\Domain\Event\SupplyArea;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class SupplyAreaCreated extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'supply_area.created';

    private SupplyAreaInterface $area;

    public function __construct(SupplyAreaInterface $area)
    {
        $this->area = $area;
    }

    public function getDispatchArea(): SupplyAreaInterface
    {
        return $this->area;
    }
}
