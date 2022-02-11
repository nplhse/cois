<?php

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\Hospital\EditHospitalCommand;
use App\Domain\Event\Hospital\HospitalEditedEvent;
use App\Repository\HospitalRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EditHospitalHandler implements HandlerInterface
{
    private HospitalRepository $hospitalRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(HospitalRepository $hospitalRepository, EventDispatcherInterface $dispatcher)
    {
        $this->hospitalRepository = $hospitalRepository;

        $this->dispatcher = $dispatcher;
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
        $hospital->setSize($command->getSize());
        $hospital->setBeds($command->getBeds());

        $this->hospitalRepository->save();

        $event = new HospitalEditedEvent($hospital);

        $this->dispatcher->dispatch($event, HospitalEditedEvent::NAME);
    }
}
