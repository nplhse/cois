<?php

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\CreateDispatchAreaHandler;
use App\Domain\Command\DispatchArea\CreateDispatchAreaCommand;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\DispatchArea\DispatchAreaCreatedEvent;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateDispatchAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('addDispatchArea')
            ->with($this->isInstanceOf(DispatchAreaInterface::class));

        $dispatchAreaRepository = $this->createMock(DispatchAreaRepository::class);
        $dispatchAreaRepository->expects($this->exactly(1))
            ->method('add')
            ->with($this->isInstanceOf(DispatchAreaInterface::class));

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($state);
        $stateRepository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(DispatchAreaCreatedEvent::class), $this->stringContains('dispatch_area.created'));

        $command = new CreateDispatchAreaCommand('Test State', $state);

        $handler = new CreateDispatchAreaHandler($dispatchAreaRepository, $stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
