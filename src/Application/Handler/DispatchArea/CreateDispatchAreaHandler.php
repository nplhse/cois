<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\DispatchArea\CreateDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaCreatedEvent;
use App\Entity\DispatchArea;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;

class CreateDispatchAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private DispatchAreaRepository $dispatchAreaRepository,
        private StateRepository $stateRepository
    ) {
    }

    public function __invoke(CreateDispatchAreaCommand $command): void
    {
        $area = new DispatchArea();
        $area->setName($command->getName());
        $area->setState($command->getState());

        $this->dispatchAreaRepository->add($area);

        $state = $this->stateRepository->getById($command->getState()->getId());
        $state->addDispatchArea($area);

        $this->stateRepository->save();

        $this->dispatchEvent(new DispatchAreaCreatedEvent($area));
    }
}
