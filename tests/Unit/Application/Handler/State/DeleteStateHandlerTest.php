<?php

namespace App\Tests\Unit\Application\Handler\State;

use App\Application\Handler\State\CreateStateHandler;
use App\Domain\Command\State\CreateStateCommand;
use App\Domain\Contracts\StateInterface;
use App\Repository\StateRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteStateHandlerTest extends TestCase
{
    public function testHandler(): void
    {
        $this->markTestSkipped();

        $state = new State();
        $state->setName('Test State');

        $state = $this->createMock(StateInterface::class);

        $stateRepository = $this->createMock(StateRepository::class);
        $stateRepository->expects($this->exactly(1))
            ->getById(1)
            ->with($this->isInstanceOf(StateInterface::class));
        $stateRepository->expects($this->exactly(1))
            ->method('delete')
            ->with($this->isInstanceOf(StateInterface::class));

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(1))
            ->method('dispatch');

        $command = new CreateStateCommand('Test State');

        $handler = new CreateStateHandler($stateRepository, $eventDispatcher);
        $handler->__invoke($command);
    }
}
