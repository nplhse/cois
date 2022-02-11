<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contracts\DomainEventInterface;

class DomainEvent implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'domain.event';
}
