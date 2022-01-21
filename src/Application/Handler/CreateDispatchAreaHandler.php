<?php

namespace App\Application\Handler;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\CreateDispatchAreaCommand;
use App\Domain\Command\CreateStateCommand;
use App\Domain\Event\DispatchAreaCreated;
use App\Domain\Event\StateCreated;
use App\Domain\Repository\StateRepositoryInterface;
use App\Entity\DispatchArea;
use App\Entity\State;
use App\Repository\DispatchAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateDispatchAreaHandler implements HandlerInterface
{
    private DispatchAreaRepository $dispatchAreaRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository, EventDispatcherInterface $dispatcher)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(CreateDispatchAreaCommand $command): void
    {
        $area = new DispatchArea();
        $area->setName($command->getName());
        $area->setState($command->getState());

        $this->dispatchAreaRepository->add($area);

        $event = new DispatchAreaCreated($area);

        $this->dispatcher->dispatch($event, DispatchAreaCreated::NAME);
    }
}
