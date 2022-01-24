<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\DispatchArea\CreateDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaCreated;
use App\Entity\DispatchArea;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateDispatchAreaHandler implements HandlerInterface
{
    private DispatchAreaRepository $dispatchAreaRepository;

    private StateRepository $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository, StateRepository $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
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

        $event = new DispatchAreaCreated($area);

        $this->dispatcher->dispatch($event, DispatchAreaCreated::NAME);
    }
}
