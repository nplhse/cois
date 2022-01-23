<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaSwitchedState extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'dispatch_area.switched_state';

    private DispatchAreaInterface $area;

    private StateInterface $state;

    public function __construct(DispatchAreaInterface $area, StateInterface $state)
    {
        $this->area = $area;
        $this->state = $state;
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
