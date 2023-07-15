<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\UpdateDispatchAreaHandler;
use App\Repository\DispatchAreaRepository;
use Domain\Command\DispatchArea\UpdateDispatchAreaCommand;
use Domain\Contracts\DispatchAreaInterface;
use Domain\Event\DispatchArea\DispatchAreaUpdatedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateDispatchAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $dispatchArea = $this->createMock(DispatchAreaInterface::class);
        $dispatchArea->expects($this->exactly(1))
            ->method('setName');

        $dispatchAreaRespository = $this->createMock(DispatchAreaRepository::class);
        $dispatchAreaRespository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($dispatchArea);
        $dispatchAreaRespository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(DispatchAreaUpdatedEvent::class), $this->stringContains('dispatch_area.updated'));

        $command = new UpdateDispatchAreaCommand(1, 'Test Name');

        $handler = new UpdateDispatchAreaHandler($dispatchAreaRespository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
