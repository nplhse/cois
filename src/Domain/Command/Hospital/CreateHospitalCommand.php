<?php

declare(strict_types=1);

namespace Domain\Command\Hospital;

use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Contracts\UserInterface;
use Domain\Enum\HospitalLocation;
use Domain\Enum\HospitalTier;

class CreateHospitalCommand
{
    public function __construct(
        private UserInterface $owner,
        private string $name,
        private string $address,
        private ?StateInterface $state,
        private ?DispatchAreaInterface $dispatchArea,
        private ?SupplyAreaInterface $supplyArea,
        private HospitalLocation $location,
        private int $beds,
        private HospitalTier $tier
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getState(): ?StateInterface
    {
        return $this->state;
    }

    public function getDispatchArea(): ?DispatchAreaInterface
    {
        return $this->dispatchArea;
    }

    public function getSupplyArea(): ?SupplyAreaInterface
    {
        return $this->supplyArea;
    }

    public function getLocation(): HospitalLocation
    {
        return $this->location;
    }

    public function getBeds(): int
    {
        return $this->beds;
    }

    public function getTier(): HospitalTier
    {
        return $this->tier;
    }
}
