<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\SupplyArea;

use App\Application\Handler\SupplyArea\DeleteSupplyAreaHandler;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Event\SupplyArea\SupplyAreaDeletedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteSupplyAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('removeSupplyArea')
            ->with($this->isInstanceOf(SupplyAreaInterface::class));
        $state->expects($this->exactly(1))
            ->method('getId')
            ->willReturn(1);

        $supplyArea = $this->createMock(SupplyAreaInterface::class);
        $supplyArea->expects($this->exactly(1))
            ->method('getState')
            ->willReturn($state);

        $supplyAreaRespository = $this->createMock(SupplyAreaRepository::class);
        $supplyAreaRespository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($supplyArea);
        $supplyAreaRespository->expects($this->exactly(1))
            ->method('delete')
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
            ->with($this->isInstanceOf(SupplyAreaDeletedEvent::class), $this->stringContains('supply_area.deleted'));

        $command = new DeleteSupplyAreaCommand(1);

        $handler = new DeleteSupplyAreaHandler($supplyAreaRespository, $stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
