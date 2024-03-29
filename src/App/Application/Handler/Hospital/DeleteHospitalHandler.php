<?php

declare(strict_types=1);

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use Domain\Command\Hospital\DeleteHospitalCommand;
use Domain\Event\Hospital\HospitalDeletedEvent;
use Domain\Repository\HospitalRepositoryInterface;

class DeleteHospitalHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private HospitalRepositoryInterface $hospitalRepository
    ) {
    }

    public function __invoke(DeleteHospitalCommand $command): void
    {
        $hospital = $this->hospitalRepository->findOneById($command->getId());

        $this->hospitalRepository->delete($hospital);

        $this->dispatchEvent(new HospitalDeletedEvent($hospital));
    }
}
