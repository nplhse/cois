<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\SupplyArea\SwitchStateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaSwitchedStateEvent;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;

class SwitchStateSupplyAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private SupplyAreaRepository $supplyAreaRepository,
        private StateRepository $stateRepository
    ) {
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

        $this->dispatchEvent(new SupplyAreaSwitchedStateEvent($area, $newState));
    }
}
