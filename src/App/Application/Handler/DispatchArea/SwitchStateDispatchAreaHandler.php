<?php

declare(strict_types=1);

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Domain\Command\DispatchArea\SwitchStateDispatchAreaCommand;
use Domain\Event\DispatchArea\DispatchAreaSwitchedStateEvent;

class SwitchStateDispatchAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private DispatchAreaRepository $dispatchAreaRepository,
        private StateRepository $stateRepository
    ) {
    }

    public function __invoke(SwitchStateDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->findOneBy(['id' => $command->getAreaId()]);

        $oldState = $this->stateRepository->getById($area->getState()->getId());
        $newState = $this->stateRepository->getById($command->getStateId());

        $area->setState($newState);

        $oldState->removeDispatchArea($area);
        $newState->addDispatchArea($area);

        $this->dispatchAreaRepository->save();

        $this->dispatchEvent(new DispatchAreaSwitchedStateEvent($area, $newState));
    }
}
