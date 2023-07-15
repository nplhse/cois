<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\CreateDispatchAreaHandler;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Domain\Command\DispatchArea\CreateDispatchAreaCommand;
use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\StateInterface;
use Domain\Event\DispatchArea\DispatchAreaCreatedEvent;
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

        $handler = new CreateDispatchAreaHandler($dispatchAreaRepository, $stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
