<?php

declare(strict_types=1);

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\DispatchAreaRepository;
use Domain\Command\DispatchArea\UpdateDispatchAreaCommand;
use Domain\Event\DispatchArea\DispatchAreaUpdatedEvent;

class UpdateDispatchAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private DispatchAreaRepository $dispatchAreaRepository
    ) {
    }

    public function __invoke(UpdateDispatchAreaCommand $command): void
    {
        $area = $this->dispatchAreaRepository->getById($command->getId());

        $area->setName($command->getName());

        $this->dispatchAreaRepository->save();

        $this->dispatchEvent(new DispatchAreaUpdatedEvent($area));
    }
}
