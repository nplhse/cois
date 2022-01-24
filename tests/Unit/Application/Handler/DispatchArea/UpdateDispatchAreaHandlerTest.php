<?php

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\UpdateDispatchAreaHandler;
use App\Domain\Command\DispatchArea\UpdateDispatchAreaCommand;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Event\DispatchArea\DispatchAreaUpdated;
use App\Repository\DispatchAreaRepository;
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
            ->with($this->isInstanceOf(DispatchAreaUpdated::class), $this->stringContains('dispatch_area.updated'));

        $command = new UpdateDispatchAreaCommand(1, 'Test Name');

        $handler = new UpdateDispatchAreaHandler($dispatchAreaRespository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
