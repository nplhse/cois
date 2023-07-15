<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\DeleteStateHandler;
use App\Repository\StateRepository;
use Domain\Command\State\DeleteStateCommand;
use Domain\Contracts\StateInterface;
use Domain\Entity\State;
use Domain\Event\State\StateDeletedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteStateHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $state = new State();
        $state->setName('Test State');

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->method('getById')
            ->willReturn($state);
        $stateRepository->expects($this->exactly(1))
            ->method('delete')
            ->with($this->isInstanceOf(StateInterface::class));

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(StateDeletedEvent::class), $this->stringContains('state.deleted'));

        $command = new DeleteStateCommand(1);

        $handler = new DeleteStateHandler($stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
