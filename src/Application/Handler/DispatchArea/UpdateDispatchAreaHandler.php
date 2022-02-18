<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\DispatchArea\UpdateDispatchAreaCommand;
use App\Domain\Event\DispatchArea\DispatchAreaUpdatedEvent;
use App\Repository\DispatchAreaRepository;

class UpdateDispatchAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private DispatchAreaRepository $dispatchAreaRepository;

    public function __construct(DispatchAreaRepository $dispatchAreaRepository)
    {
        $this->dispatchAreaRepository = $dispatchAreaRepository;
    }

    public function __invoke(UpdateDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getId());

        $area->setName($command->getName());

        $this->dispatchAreaRepository->save();

        $this->dispatchEvent(new DispatchAreaUpdatedEvent($area));
    }
}
