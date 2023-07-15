<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\UpdateStateHandler;
use App\Repository\StateRepository;
use Domain\Command\State\UpdateStateCommand;
use Domain\Contracts\StateInterface;
use Domain\Event\State\StateUpdatedEvent;
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
            ->with($this->isInstanceOf(StateUpdatedEvent::class), $this->stringContains('state.updated'));

        $command = new UpdateStateCommand(1, 'Updated Name');

        $handler = new UpdateStateHandler($stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
