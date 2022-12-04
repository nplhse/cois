<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Event;

use App\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class DomainEventTest extends TestCase
{
    public function testNamedEvent(): void
    {
        $event = new DomainEvent();

        $this->assertEquals('domain.event', $event->getName());
    }
}
