<?php

declare(strict_types=1);

namespace Domain\Event\DispatchArea;

use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaCreatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'dispatch_area.created';

    public function __construct(
        private DispatchAreaInterface $area
    ) {
    }

    public function getDispatchArea(): DispatchAreaInterface
    {
        return $this->area;
    }
}
