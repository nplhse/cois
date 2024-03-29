<?php

declare(strict_types=1);

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\Hospital;
use App\Repository\HospitalRepository;
use Domain\Command\Hospital\CreateHospitalCommand;
use Domain\Event\Hospital\HospitalCreatedEvent;

class CreateHospitalHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private HospitalRepository $hospitalRepository
    ) {
    }

    public function __invoke(CreateHospitalCommand $command): void
    {
        $hospital = new Hospital();
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

        $this->hospitalRepository->add($hospital);

        $this->dispatchEvent(new HospitalCreatedEvent($hospital));
    }
}
