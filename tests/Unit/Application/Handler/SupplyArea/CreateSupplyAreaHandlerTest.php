<?php

namespace App\Tests\Unit\Application\Handler\SupplyArea;

use App\Application\Handler\SupplyArea\CreateSupplyAreaHandler;
use App\Domain\Command\SupplyArea\CreateSupplyAreaCommand;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Event\SupplyArea\SupplyAreaCreated;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateSupplyAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('addSupplyArea')
            ->with($this->isInstanceOf(SupplyAreaInterface::class));

        $supplyAreaRepository = $this->createMock(SupplyAreaRepository::class);
        $supplyAreaRepository->expects($this->exactly(1))
            ->method('add')
            ->with($this->isInstanceOf(SupplyAreaInterface::class));

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($state);
        $stateRepository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(SupplyAreaCreated::class), $this->stringContains('supply_area.created'));

        $command = new CreateSupplyAreaCommand('Test Area', 1);

        $handler = new CreateSupplyAreaHandler($supplyAreaRepository, $stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
