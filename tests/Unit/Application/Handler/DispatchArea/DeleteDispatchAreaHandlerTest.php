<?php

namespace App\Tests\Unit\Application\Handler\DispatchArea;

use App\Application\Handler\DispatchArea\DeleteDispatchAreaHandler;
use App\Domain\Command\DispatchArea\DeleteDispatchAreaCommand;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\DispatchArea\DispatchAreaDeleted;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteDispatchAreaHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = $this->createMock(StateInterface::class);
        $state->expects($this->exactly(1))
            ->method('removeDispatchArea')
            ->with($this->isInstanceOf(DispatchAreaInterface::class));
        $state->expects($this->exactly(1))
            ->method('getId')
            ->willReturn(1);

        $dispatchArea = $this->createMock(DispatchAreaInterface::class);
        $dispatchArea->expects($this->exactly(1))
            ->method('getState')
            ->willReturn($state);

        $dispatchAreaRespository = $this->createMock(DispatchAreaRepository::class);
        $dispatchAreaRespository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($dispatchArea);
        $dispatchAreaRespository->expects($this->exactly(1))
            ->method('delete')
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
            ->with($this->isInstanceOf(DispatchAreaDeleted::class), $this->stringContains('dispatch_area.deleted'));

        $command = new DeleteDispatchAreaCommand(1);

        $handler = new DeleteDispatchAreaHandler($dispatchAreaRespository, $stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}