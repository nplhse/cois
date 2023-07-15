<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Adapter\ArrayCollection;
use Domain\Contracts\HospitalInterface;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Entity\Traits\IdentifierTrait;
use Domain\Entity\Traits\TimestampableTrait;

class SupplyArea implements SupplyAreaInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    protected string $name;

    protected StateInterface $state;

    protected \Doctrine\Common\Collections\Collection $hospitals;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->hospitals = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setState(StateInterface $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }

    public function addHospital(HospitalInterface $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals[] = $hospital;

            $hospital->setSupplyArea($this);
        }

        return $this;
    }

    public function removeHospital(HospitalInterface $hospital): self
    {
        $this->hospitals->removeElement($hospital);

        return $this;
    }

    public function getHospitals(): \Doctrine\Common\Collections\Collection
    {
        return $this->hospitals;
    }
}
