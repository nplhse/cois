<?php

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\State\UpdateStateCommand;
use App\Domain\Event\State\StateUpdatedEvent;
use App\Domain\Repository\StateRepositoryInterface;

class UpdateStateHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private StateRepositoryInterface $stateRepository;

    public function __construct(StateRepositoryInterface $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function __invoke(UpdateStateCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getId());

        $state->setName($command->getName());

        $this->stateRepository->save();

        $this->dispatchEvent(new StateUpdatedEvent($state));
    }
}
