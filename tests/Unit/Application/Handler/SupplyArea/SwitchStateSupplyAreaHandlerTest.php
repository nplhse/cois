<?php

namespace App\Tests\Unit\Application\Handler\SupplyArea;

use App\Application\Handler\SupplyArea\SwitchStateSupplyAreaHandler;
use App\Domain\Command\SupplyArea\SwitchStateSupplyAreaCommand;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Event\SupplyArea\SupplyAreaSwitchedStateEvent;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwitchStateSupplyAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $oldState = $this->createMock(StateInterface::class);
        $oldState->expects($this->exactly(1))
            ->method('getId')
            ->willReturn(1);
        $oldState->expects($this->exactly(1))
            ->method('removeSupplyArea')
            ->with($this->isInstanceOf(SupplyAreaInterface::class));

        $newState = $this->createMock(StateInterface::class);
        $newState->expects($this->exactly(1))
            ->method('addSupplyArea')
            ->with($this->isInstanceOf(SupplyAreaInterface::class));

        $supplyArea = $this->createMock(SupplyAreaInterface::class);
        $supplyArea->expects($this->exactly(1))
            ->method('setState')
            ->with($this->isInstanceOf(StateInterface::class));
        $supplyArea->expects($this->exactly(1))
            ->method('getState')
            ->willReturn($oldState);

        $supplyAreaRepository = $this->createMock(SupplyAreaRepository::class);
        $supplyAreaRepository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($supplyArea);
        $supplyAreaRepository->expects($this->exactly(1))
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
            ->with($this->isInstanceOf(SupplyAreaSwitchedStateEvent::class), $this->stringContains('supply_area.switched_state'));

        $command = new SwitchStateSupplyAreaCommand(1, 2);

        $handler = new SwitchStateSupplyAreaHandler($supplyAreaRepository, $stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
