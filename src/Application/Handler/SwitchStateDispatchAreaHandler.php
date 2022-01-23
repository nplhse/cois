<?php

namespace App\Application\Handler;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\SwitchStateDispatchAreaCommand;
use App\Domain\Command\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchAreaDeleted;
use App\Domain\Event\DispatchAreaSwitchedState;
use App\Domain\Event\DispatchAreaUpdated;
use App\Domain\Event\StateDeleted;
use App\Domain\Repository\StateRepositoryInterface;
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
        $state = $this->stateRepository->getById($command->getStateId());

        $area->setState($state);
        $state->removeDispatchArea($area);

        $this->dispatchAreaRepository->save();
        $this->stateRepository->save();

        $event = new DispatchAreaSwitchedState($area, $state);

        $this->dispatcher->dispatch($event, DispatchAreaSwitchedState::NAME);
    }
}
