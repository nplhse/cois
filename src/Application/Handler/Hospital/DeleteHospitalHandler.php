<?php

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\Hospital\DeleteHospitalCommand;
use App\Domain\Event\Hospital\HospitalDeletedEvent;
use App\Domain\Repository\HospitalRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteHospitalHandler implements HandlerInterface
{
    private HospitalRepositoryInterface $hospitalRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(HospitalRepositoryInterface $hospitalRepository, EventDispatcherInterface $dispatcher)
    {
        $this->hospitalRepository = $hospitalRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteHospitalCommand $command): void
    {
        $hospital = $this->hospitalRepository->findOneById($command->getId());

        $this->hospitalRepository->delete($hospital);

        $event = new HospitalDeletedEvent($hospital);

        $this->dispatcher->dispatch($event, HospitalDeletedEvent::NAME);
    }
}
