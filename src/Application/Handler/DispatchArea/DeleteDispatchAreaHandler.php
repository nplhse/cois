<?php

namespace App\Application\Handler\DispatchArea;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\DispatchAreaNotEmptyException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\DispatchArea\DeleteDispatchAreaCommand;
use App\Domain\Entity\DispatchArea;
use App\Domain\Event\DispatchArea\DispatchAreaDeletedEvent;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;

class DeleteDispatchAreaHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private DispatchAreaRepository $dispatchAreaRepository,
        private StateRepository $stateRepository
    ) {
    }

    public function __invoke(DeleteDispatchAreaCommand $command): void
    {
        /** @var DispatchArea $area */
        $area = $this->dispatchAreaRepository->getById($command->getId());

        if (!$area->getHospitals()->isEmpty()) {
            throw new DispatchAreaNotEmptyException(sprintf('Cannot delete Area "%s" (#%d) as it still contains Hospitals', $area->getName(), $area->getId()));
        }

        $this->dispatchAreaRepository->delete($area);

        $state = $this->stateRepository->findOneBy(['id' => $area->getState()->getId()]);
        $state->removeDispatchArea($area);

        $this->stateRepository->save();

        $this->dispatchEvent(new DispatchAreaDeletedEvent($area));
    }
}
