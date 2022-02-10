<?php

declare(strict_types=1);

namespace App\Domain\Event\DispatchArea;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaCreatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'dispatch_area.created';

    private DispatchAreaInterface $area;

    public function __construct(DispatchAreaInterface $area)
    {
        $this->area = $area;
    }

    public function getDispatchArea(): DispatchAreaInterface
    {
        return $this->area;
    }
}
