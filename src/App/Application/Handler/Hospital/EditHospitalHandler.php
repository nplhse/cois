<?php

declare(strict_types=1);

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\HospitalRepository;
use Domain\Command\Hospital\EditHospitalCommand;
use Domain\Event\Hospital\HospitalEditedEvent;

class EditHospitalHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private HospitalRepository $hospitalRepository
    ) {
    }

    public function __invoke(EditHospitalCommand $command): void
    {
        $hospital = $this->hospitalRepository->findOneById($command->getId());

        $hospital->setOwner($command->getOwner());

        $hospital->setName($command->getName());
        $hospital->setAddress($command->getAddress());

        $hospital->setState($command->getState());
        $hospital->setDispatchArea($command->getDispatchArea());

        if ($command->getSupplyArea()) {
            $hospital->setSupplyArea($command->getSupplyArea());
        }

        $hospital->setLocation($command->getLocation());
        $hospital->setTier($command->getTier());
        $hospital->setBeds($command->getBeds());

        $this->hospitalRepository->save();

        $this->dispatchEvent(new HospitalEditedEvent($hospital));
    }
}
