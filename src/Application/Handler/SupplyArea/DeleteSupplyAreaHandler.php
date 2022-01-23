<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use App\Domain\Event\DispatchAreaDeleted;
use App\Domain\Event\StateDeleted;
use App\Domain\Event\SupplyArea\SupplyAreaDeleted;
use App\Domain\Repository\StateRepositoryInterface;
use App\Repository\DispatchAreaRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteSupplyAreaHandler implements HandlerInterface
{
    private SupplyAreaRepository $supplyAreaRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(SupplyAreaRepository $supplyAreaRepository, EventDispatcherInterface $dispatcher)
    {
        $this->supplyAreaRepository = $supplyAreaRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getId());

        // @TODO Check if SupplyArea includes any hospitals!

        $this->supplyAreaRepository->delete($area);

        $event = new SupplyAreaDeleted($area);

        $this->dispatcher->dispatch($event, SupplyAreaDeleted::NAME);
    }
}
