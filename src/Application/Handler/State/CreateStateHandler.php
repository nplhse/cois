<?php

declare(strict_types=1);

namespace App\Application\Handler\State;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\State\CreateStateCommand;
use App\Domain\Event\State\StateCreatedEvent;
use App\Domain\Repository\StateRepositoryInterface;
use App\Entity\State;

class CreateStateHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private StateRepositoryInterface $stateRepository
    ) {
    }

    public function __invoke(CreateStateCommand $command): void
    {
        $state = new State();
        $state->setName($command->getName());

        $this->stateRepository->add($state);

        $this->dispatchEvent(new StateCreatedEvent($state));
    }
}
