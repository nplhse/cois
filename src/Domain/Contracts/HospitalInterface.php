<?php

namespace App\Domain\Contracts;

use App\Domain\Adapter\ArrayCollection;

interface HospitalInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setOwner(UserInterface $user): self;

    public function getOwner(): UserInterface;

    public function addAssociatedUser(UserInterface $user): self;

    public function removeAssociatedUser(UserInterface $user): self;

    public function getAssociatedUsers(): ArrayCollection;

    public function setAddress(string $address): self;

    public function getAddress(): string;

    public function setState(StateInterface $state): self;

    public function getState(): StateInterface;

    public function setDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function getDispatchArea(): DispatchAreaInterface;

    public function setSupplyArea(SupplyAreaInterface $supplyArea): self;

    public function getSupplyArea(): SupplyAreaInterface;

    public function setSize(string $size): self;

    public function getSize(): string;

    public function setBeds(int $beds): self;

    public function getBeds(): int;

    public function setLocation(string $location): self;

    public function getLocation(): string;
}
