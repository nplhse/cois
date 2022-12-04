<?php

declare(strict_types=1);

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\SupplyArea\CreateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaCreatedEvent;
use App\Entity\SupplyArea;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;

class CreateSupplyAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private SupplyAreaRepository $supplyAreaRepository,
        private StateRepository $stateRepository
    ) {
    }

    public function __invoke(CreateSupplyAreaCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getStateId());

        $area = new SupplyArea();
        $area->setName($command->getName());
        $area->setState($state);

        $this->supplyAreaRepository->add($area);

        $state->addSupplyArea($area);

        $this->stateRepository->save();

        $this->dispatchEvent(new SupplyAreaCreatedEvent($area));
    }
}
