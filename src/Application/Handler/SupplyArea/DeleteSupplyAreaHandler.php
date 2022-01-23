<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaDeleted;
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
