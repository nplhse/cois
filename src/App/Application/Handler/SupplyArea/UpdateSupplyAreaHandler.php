<?php

declare(strict_types=1);

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\SupplyAreaRepository;
use Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;

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
