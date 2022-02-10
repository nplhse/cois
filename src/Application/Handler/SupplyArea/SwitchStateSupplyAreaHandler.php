<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\SupplyArea\SwitchStateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaSwitchedStateEvent;
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

        $oldState = $this->stateRepository->getById($area->getState()->getId());
        $newState = $this->stateRepository->getById($command->getStateId());

        $area->setState($newState);

        $oldState->removeSupplyArea($area);
        $newState->addSupplyArea($area);

        $this->supplyAreaRepository->save();
        $this->stateRepository->save();

        $event = new SupplyAreaSwitchedStateEvent($area, $newState);

        $this->dispatcher->dispatch($event, SupplyAreaSwitchedStateEvent::NAME);
    }
}
