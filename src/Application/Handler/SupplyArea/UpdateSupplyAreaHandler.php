<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaUpdatedEvent;
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

        $this->supplyAreaRepository->save();

        $event = new SupplyAreaUpdatedEvent($area);

        $this->dispatcher->dispatch($event, SupplyAreaUpdatedEvent::NAME);
    }
}
