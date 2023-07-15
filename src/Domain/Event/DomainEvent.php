<?php

declare(strict_types=1);

namespace Domain\Event;

use Domain\Contracts\DomainEventInterface;

class DomainEvent implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'domain.event';
}
