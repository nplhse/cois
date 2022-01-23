<?php

namespace App\Application\Handler;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchAreaDeleted;
use App\Domain\Event\DispatchAreaUpdated;
use App\Domain\Event\StateDeleted;
use App\Domain\Repository\StateRepositoryInterface;
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

        $this->dispatchAreaRepository->save($area);

        $event = new DispatchAreaUpdated($area);

        $this->dispatcher->dispatch($event, DispatchAreaUpdated::NAME);
    }
}
