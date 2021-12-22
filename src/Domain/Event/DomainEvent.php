<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\Traits\NamedEventTrait;

class DomainEvent implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'domain.event';
}
