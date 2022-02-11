<?php

namespace App\Domain\Command\Hospital;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Contracts\UserInterface;

class EditHospitalCommand
{
    private int $id;

    private UserInterface $owner;

    private string $name;

    private string $address;

    private ?StateInterface $state;

    private ?DispatchAreaInterface $dispatchArea;

    private ?SupplyAreaInterface $supplyArea;

    private string $location;

    private int $beds;

    private string $size;

    public function __construct(int $id, UserInterface $owner, string $name, string $address, ?StateInterface $state, ?DispatchAreaInterface $dispatchArea, ?SupplyAreaInterface $supplyArea, string $location, int $beds, string $size)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
        $this->address = $address;
        $this->state = $state;
        $this->dispatchArea = $dispatchArea;
        $this->supplyArea = $supplyArea;
        $this->location = $location;
        $this->beds = $beds;
        $this->size = $size;
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getBeds(): int
    {
        return $this->beds;
    }

    public function getSize(): string
    {
        return $this->size;
    }
}