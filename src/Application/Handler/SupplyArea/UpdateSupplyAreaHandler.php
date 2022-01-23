<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use App\Domain\Command\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchAreaDeleted;
use App\Domain\Event\StateDeleted;
use App\Domain\Event\SupplyArea\SupplyAreaUpdated;
use App\Domain\Repository\StateRepositoryInterface;
use App\Repository\DispatchAreaRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateSupplyAreaHandler implements HandlerInterface
{
    private SupplyAreaRepository $supplyAreaRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(SupplyAreaRepository $supplyAreaRepository, EventDispatcherInterface $dispatcher)
    {
        $this->supplyAreaRepository = $supplyAreaRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(UpdateSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getId());

        $area->setName($command->getName());

        $this->supplyAreaRepository->save($area);

        $event = new SupplyAreaUpdated($area);

        $this->dispatcher->dispatch($event, SupplyAreaUpdated::NAME);
    }
}
