<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\CreateStateHandler;
use App\Repository\StateRepository;
use Domain\Command\State\CreateStateCommand;
use Domain\Contracts\StateInterface;
use Domain\Event\State\StateCreatedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateStateHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->method('add')
            ->with($this->isInstanceOf(StateInterface::class));

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(StateCreatedEvent::class), $this->stringContains('state.created'));

        $command = new CreateStateCommand('Test State');

        $handler = new CreateStateHandler($stateRepository);
        $handler->setEventDispatcher($eventDispatcher);

        $handler->__invoke($command);
    }
}
