<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\SwitchStateDispatchAreaHandler;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Domain\Command\DispatchArea\SwitchStateDispatchAreaCommand;
use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\StateInterface;
use Domain\Event\DispatchArea\DispatchAreaSwitchedStateEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwitchStateDispatchAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $oldState = $this->createMock(StateInterface::class);
        $oldState->expects($this->exactly(1))
            ->method('getId')
            ->willReturn(1);
        $oldState->expects($this->exactly(1))
            ->method('removeDispatchArea')
            ->with($this->isInstanceOf(DispatchAreaInterface::class));

        $newState = $this->createMock(StateInterface::class);
        $newState->expects($this->exactly(1))
            ->method('addDispatchArea')
            ->with($this->isInstanceOf(DispatchAreaInterface::class));

        $area = $this->createMock(DispatchAreaInterface::class);
        $area->expects($this->exactly(1))
            ->method('setState')
            ->with($this->isInstanceOf(StateInterface::class));
        $area->expects($this->exactly(1))
            ->method('getState')
            ->willReturn($oldState);

        $dispatchAreaRepository = $this->createMock(DispatchAreaRepository::class);
        $dispatchAreaRepository->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($area);
        $dispatchAreaRepository->expects($this->exactly(1))
            ->method('save');

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(2))
            ->method('getById')
            ->willReturnMap([
                [1, $oldState],
                [2, $newState],
            ]);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(DispatchAreaSwitchedStateEvent::class), $this->stringContains('dispatch_area.switched_state'));

        $command = new SwitchStateDispatchAreaCommand(1, 2);

        $handler = new SwitchStateDispatchAreaHandler($dispatchAreaRepository, $stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
