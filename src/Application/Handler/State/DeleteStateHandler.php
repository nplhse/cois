<?php

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\State\DeleteStateCommand;
use App\Domain\Event\State\StateDeletedEvent;
use App\Domain\Repository\StateRepositoryInterface;

class DeleteStateHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private StateRepositoryInterface $stateRepository;

    public function __construct(StateRepositoryInterface $stateRepository)
    {
        $this->stateRepository = $stateRepository;
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

        $this->dispatchEvent(new StateDeletedEvent($state));
    }
}
