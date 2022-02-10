<?php

namespace App\Application\Handler\SupplyArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\SupplyArea\CreateSupplyAreaCommand;
use App\Domain\Event\SupplyArea\SupplyAreaCreatedEvent;
use App\Entity\SupplyArea;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateSupplyAreaHandler implements HandlerInterface
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

    public function __invoke(CreateSupplyAreaCommand $command): void
    {
        $state = $this->stateRepository->getById($command->getStateId());

        $area = new SupplyArea();
        $area->setName($command->getName());
        $area->setState($state);

        $this->supplyAreaRepository->add($area);

        $state->addSupplyArea($area);

        $this->stateRepository->save();

        $event = new SupplyAreaCreatedEvent($area);

        $this->dispatcher->dispatch($event, SupplyAreaCreatedEvent::NAME);
    }
}
