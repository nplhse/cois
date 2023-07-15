<?php

declare(strict_types=1);

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use Domain\Command\State\UpdateStateCommand;
use Domain\Event\State\StateUpdatedEvent;
use Domain\Repository\StateRepositoryInterface;

class UpdateStateHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private StateRepositoryInterface $stateRepository
    ) {
    }

    public function __invoke(UpdateStateCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getId());

        $state->setName($command->getName());

        $this->stateRepository->save();

        $this->dispatchEvent(new StateUpdatedEvent($state));
    }
}
