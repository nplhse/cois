<?php

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\SwitchStateDispatchAreaHandler;
use App\Domain\Command\DispatchArea\SwitchStateDispatchAreaCommand;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\DispatchArea\DispatchAreaSwitchedStateEvent;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
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
            ->method('getById')
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
        $stateRepository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(DispatchAreaSwitchedStateEvent::class), $this->stringContains('dispatch_area.switched_state'));

        $command = new SwitchStateDispatchAreaCommand(1, 2);

        $handler = new SwitchStateDispatchAreaHandler($dispatchAreaRepository, $stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
