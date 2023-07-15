<?php

declare(strict_types=1);

namespace App\Domain\Event\DispatchArea;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaUpdatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'dispatch_area.updated';

    public function __construct(
        private DispatchAreaInterface $area
    ) {
    }

    public function getDispatchArea(): DispatchAreaInterface
    {
        return $this->area;
    }
}
