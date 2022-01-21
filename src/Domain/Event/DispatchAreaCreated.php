<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class DispatchAreaCreated extends Event implements DomainEventInterface
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
