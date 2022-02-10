<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\DispatchArea\DeleteDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaDeletedEvent;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteDispatchAreaHandler implements HandlerInterface
{
    private DispatchAreaRepository $dispatchAreaRepository;

    private StateRepository $stateRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository, StateRepository $stateRepository, EventDispatcherInterface $dispatcher)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;
        $this->stateRepository = $stateRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getId());

        // @TODO Check if DispatchArea includes any hospitals!

        $this->dispatchAreaRepository->delete($area);

        $state = $this->stateRepository->getById($area->getState()->getId());
        $state->removeDispatchArea($area);

        $this->stateRepository->save();

        $event = new DispatchAreaDeletedEvent($area);

        $this->dispatcher->dispatch($event, DispatchAreaDeletedEvent::NAME);
    }
}
