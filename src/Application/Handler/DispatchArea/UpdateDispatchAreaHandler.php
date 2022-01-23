<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\DispatchArea\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaUpdated;
use App\Domain\Event\SupplyArea\SupplyAreaUpdated;
use App\Repository\DispatchAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateDispatchAreaHandler implements HandlerInterface
{
    private DispatchAreaRepository $dispatchAreaRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository, EventDispatcherInterface $dispatcher)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(UpdateDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getId());

        $area->setName($command->getName());

        $this->dispatchAreaRepository->save();

        $event = new DispatchAreaUpdated($area);

        $this->dispatcher->dispatch($event, SupplyAreaUpdated::NAME);
    }
}
