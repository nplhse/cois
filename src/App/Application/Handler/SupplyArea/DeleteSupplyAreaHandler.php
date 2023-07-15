<?php

declare(strict_types=1);

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use Domain\Event\SupplyArea\SupplyAreaDeletedEvent;

class DeleteSupplyAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private SupplyAreaRepository $supplyAreaRepository,
        private StateRepository $stateRepository
    ) {
    }

    public function __invoke(DeleteSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getId());

        // @TODO Check if SupplyArea includes any hospitals!

        $this->supplyAreaRepository->delete($area);

        $state = $this->stateRepository->getById($area->getState()->getId());
        $state->removeSupplyArea($area);

        $this->stateRepository->save();

        $this->dispatchEvent(new SupplyAreaDeletedEvent($area));
    }
}
