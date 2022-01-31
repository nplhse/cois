<?php

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\State\DeleteStateCommand;
use App\Domain\Event\State\StateDeleted;
use App\Domain\Repository\StateRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteStateHandler implements HandlerInterface
{
    private StateRepositoryInterface $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(StateRepositoryInterface $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteStateCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getId());

        if (!$state->getDispatchAreas()->isEmpty()) {
            throw new StateNotEmptyException('Cannot delete Area as it still contains Dispatch Areas');
        }

        if (!$state->getSupplyAreas()->isEmpty()) {
            throw new StateNotEmptyException('Cannot delete Area as it still contains Supply Areas');
        }

        $this->stateRepository->delete($state);

        $event = new StateDeleted($state);

        $this->dispatcher->dispatch($event, StateDeleted::NAME);
    }
}