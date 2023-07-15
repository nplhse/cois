<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\SupplyArea;

use App\Application\Handler\SupplyArea\UpdateSupplyAreaHandler;
use App\Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;
use App\Repository\SupplyAreaRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateSupplyAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $dispatchArea = $this->createMock(SupplyAreaInterface::class);
        $dispatchArea->expects($this->exactly(1))
            ->method('setName');

        $supplyAreaRespository = $this->createMock(SupplyAreaRepository::class);
        $supplyAreaRespository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($dispatchArea);
        $supplyAreaRespository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(SupplyAreaUpdatedEvent::class), $this->stringContains('supply_area.updated'));

        $command = new UpdateSupplyAreaCommand(1, 'Test Name');

        $handler = new UpdateSupplyAreaHandler($supplyAreaRespository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
