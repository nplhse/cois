<?php

namespace App\Application\Handler;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\UpdateStateCommand;
use App\Domain\Event\StateUpdated;
use App\Domain\Repository\StateRepositoryInterface;
use App\Entity\State;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateStateHandler implements HandlerInterface
{
    private StateRepositoryInterface $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(StateRepositoryInterface $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(UpdateStateCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getId());
        $state->setName($command->getName());

        $this->stateRepository->save();

        $event = new StateUpdated($state);

        $this->dispatcher->dispatch($event, StateUpdated::NAME);
    }
}
