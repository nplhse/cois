<?php

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\UpdateStateHandler;
use App\Domain\Command\State\UpdateStateCommand;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\State\StateUpdated;
use App\Repository\StateRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateStateHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = $this->createMock(StateInterface::class);
        $state->expects($this->any())
            ->method('setName');

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->any())
            ->method('getById')
            ->willReturn($state);

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->method('save');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(StateUpdated::class), $this->stringContains('state.updated'));

        $command = new UpdateStateCommand(1, 'Updated Name');

        $handler = new UpdateStateHandler($stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
