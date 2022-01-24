<?php

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\CreateStateHandler;
use App\Domain\Command\State\CreateStateCommand;
use App\Domain\Contracts\StateInterface;
use App\Domain\Event\State\StateCreated;
use App\Repository\StateRepository;
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
            ->with($this->isInstanceOf(StateCreated::class), $this->stringContains('state.created'));

        $command = new CreateStateCommand('Test State');

        $handler = new CreateStateHandler($stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
