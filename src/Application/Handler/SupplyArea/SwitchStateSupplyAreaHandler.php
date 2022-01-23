<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\SupplyArea\SwitchStateSupplyAreaCommand;
use App\Domain\Command\SwitchStateDispatchAreaCommand;
use App\Domain\Command\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchAreaDeleted;
use App\Domain\Event\DispatchAreaSwitchedState;
use App\Domain\Event\SupplyArea\SupplyAreaSwitchedState;
use App\Domain\Event\SupplyAreaUpdated;
use App\Domain\Event\StateDeleted;
use App\Domain\Repository\StateRepositoryInterface;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwitchStateSupplyAreaHandler implements HandlerInterface
{
    private SupplyAreaRepository $supplyAreaRepository;

    private StateRepository $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(SupplyAreaRepository $supplyAreaRepository, StateRepository $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->supplyAreaRepository = $supplyAreaRepository;
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(SwitchStateSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getAreaId());

        $newState = $this->stateRepository->getById($command->getStateId());
        $oldState = $area->getState();

        $area->setState($newState);

        $oldState->removeSupplyArea($area);
        $newState->addSupplyArea($area);

        $this->supplyAreaRepository->save();
        $this->stateRepository->save();

        $event = new SupplyAreaSwitchedState($area, $newState);

        $this->dispatcher->dispatch($event, SupplyAreaSwitchedState::NAME);
    }
}
