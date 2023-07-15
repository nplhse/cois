<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Adapter\ArrayCollection;
use App\Domain\Enum\HospitalLocation;
use App\Domain\Enum\HospitalSize;
use Doctrine\Common\Collections\Collection;

interface HospitalInterface
{
    public function getId(): int;

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

    public function getState(): ?StateInterface;

    public function setDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function getDispatchArea(): ?DispatchAreaInterface;

    public function setSupplyArea(?SupplyAreaInterface $supplyArea): self;

    public function getSupplyArea(): ?SupplyAreaInterface;

    public function setSize(HospitalSize $size): self;

    public function getSize(): HospitalSize;

    public function setBeds(int $beds): self;

    public function getBeds(): int;

    public function setLocation(HospitalLocation $location): self;

    public function getLocation(): HospitalLocation;

    public function addImport(ImportInterface $import): self;

    public function removeImport(ImportInterface $import): self;

    public function getImports(): Collection;
}
