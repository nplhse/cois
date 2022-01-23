<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\DispatchArea\SwitchStateDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaSwitchedState;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwitchStateDispatchAreaHandler implements HandlerInterface
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

    public function __invoke(SwitchStateDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getAreaId());

        $oldState = $area->getState();
        $newState = $this->stateRepository->getById($command->getStateId());

        $area->setState($newState);

        $oldState->removeDispatchArea($area);
        $newState->addDispatchArea($area);

        $this->dispatchAreaRepository->save();
        $this->stateRepository->save();

        $event = new DispatchAreaSwitchedState($area, $newState);

        $this->dispatcher->dispatch($event, DispatchAreaSwitchedState::NAME);
    }
}
