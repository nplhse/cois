<?php

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\State\CreateStateCommand;
use App\Domain\Event\State\StateCreatedEvent;
use App\Domain\Repository\StateRepositoryInterface;
use App\Entity\State;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateStateHandler implements HandlerInterface
{
    private StateRepositoryInterface $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(StateRepositoryInterface $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(CreateStateCommand $command): void
    {
        $state = new State();
        $state->setName($command->getName());

        $this->stateRepository->add($state);

        $event = new StateCreatedEvent($state);

        $this->dispatcher->dispatch($event, StateCreatedEvent::NAME);
    }
}
