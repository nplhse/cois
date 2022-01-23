<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\DispatchArea\DeleteDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaDeleted;
use App\Repository\DispatchAreaRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteDispatchAreaHandler implements HandlerInterface
{
    private DispatchAreaRepository $dispatchAreaRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository, EventDispatcherInterface $dispatcher)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getId());

        // @TODO Check if DispatchArea includes any hospitals!

        $this->dispatchAreaRepository->delete($area);

        $event = new DispatchAreaDeleted($area);

        $this->dispatcher->dispatch($event, DispatchAreaDeleted::NAME);
    }
}
