<?php

namespace App\Application\Handler\Hospital;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\Hospital\CreateHospitalCommand;
use App\Domain\Event\Hospital\HospitalCreatedEvent;
use App\Entity\Hospital;
use App\Repository\HospitalRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateHospitalHandler implements HandlerInterface
{
    private HospitalRepository $hospitalRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(HospitalRepository $hospitalRepository, EventDispatcherInterface $dispatcher)
    {
        $this->hospitalRepository = $hospitalRepository;

        $this->dispatcher = $dispatcher;
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
        $hospital->setSize($command->getSize());
        $hospital->setBeds($command->getBeds());

        dump($hospital);

        $this->hospitalRepository->add($hospital);

        $event = new HospitalCreatedEvent($hospital);

        $this->dispatcher->dispatch($event, HospitalCreatedEvent::NAME);
    }
}
