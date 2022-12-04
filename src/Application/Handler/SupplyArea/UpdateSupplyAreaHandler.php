<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;
use App\Repository\SupplyAreaRepository;

class UpdateSupplyAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private SupplyAreaRepository $supplyAreaRepository
    ) {
    }

    public function __invoke(UpdateSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getId());

        $area->setName($command->getName());

        $this->supplyAreaRepository->save();

        $this->dispatchEvent(new SupplyAreaUpdatedEvent($area));
    }
}
