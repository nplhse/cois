<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaDeletedEvent;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteSupplyAreaHandler implements HandlerInterface
{
    private SupplyAreaRepository $supplyAreaRepository;

    private StateRepository $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(SupplyAreaRepository $supplyAreaRepository, StateRepository $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->supplyAreaRepository = $supplyAreaRepository;
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteSupplyAreaCommand $command): void
    {
        $area = $this->supplyAreaRepository->getById($command->getId());

        // @TODO Check if SupplyArea includes any hospitals!

        $this->supplyAreaRepository->delete($area);

        $state = $this->stateRepository->getById($area->getState()->getId());
        $state->removeSupplyArea($area);

        $this->stateRepository->save();

        $event = new SupplyAreaDeletedEvent($area);

        $this->dispatcher->dispatch($event, SupplyAreaDeletedEvent::NAME);
    }
}
