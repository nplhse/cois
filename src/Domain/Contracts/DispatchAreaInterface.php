<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface DispatchAreaInterface
{
    public function getId(): int;

    public function setName(string $name): self;

    public function getName(): string;

    public function setState(StateInterface $state): self;

    public function getState(): StateInterface;

    public function addHospital(HospitalInterface $hospital): self;

    public function removeHospital(HospitalInterface $hospital): self;

    public function getHospitals(): \Doctrine\Common\Collections\Collection;
}
