<?php

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Hospital\DeleteHospitalCommand;
use App\Domain\Event\Hospital\HospitalDeletedEvent;
use App\Domain\Repository\HospitalRepositoryInterface;

class DeleteHospitalHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private HospitalRepositoryInterface $hospitalRepository;

    public function __construct(HospitalRepositoryInterface $hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
    }

    public function __invoke(DeleteHospitalCommand $command): void
    {
        $hospital = $this->hospitalRepository->findOneById($command->getId());

        $this->hospitalRepository->delete($hospital);

        $this->dispatchEvent(new HospitalDeletedEvent($hospital));
    }
}
