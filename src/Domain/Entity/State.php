<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Adapter\ArrayCollection;
use Domain\Contracts\DispatchAreaInterface;
use Domain\Contracts\HospitalInterface;
use Domain\Contracts\StateInterface;
use Domain\Contracts\SupplyAreaInterface;
use Domain\Entity\Traits\IdentifierTrait;
use Domain\Entity\Traits\TimestampableTrait;

class State implements StateInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    protected string $name;

    protected \Doctrine\Common\Collections\Collection $dispatchAreas;

    protected \Doctrine\Common\Collections\Collection $supplyAreas;

    protected \Doctrine\Common\Collections\Collection $hospitals;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->dispatchAreas = new ArrayCollection();
        $this->supplyAreas = new ArrayCollection();
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

    public function addDispatchArea(DispatchAreaInterface $dispatchArea): self
    {
        if (!$this->dispatchAreas->contains($dispatchArea)) {
            $this->dispatchAreas[] = $dispatchArea;

            $dispatchArea->setState($this);
        }

        return $this;
    }

    public function removeDispatchArea(DispatchAreaInterface $dispatchArea): self
    {
        $this->dispatchAreas->removeElement($dispatchArea);

        return $this;
    }

    public function getDispatchAreas(): \Doctrine\Common\Collections\Collection
    {
        return $this->dispatchAreas;
    }

    public function addSupplyArea(SupplyAreaInterface $supplyArea): self
    {
        if (!$this->supplyAreas->contains($supplyArea)) {
            $this->supplyAreas[] = $supplyArea;

            $supplyArea->setState($this);
        }

        return $this;
    }

    public function removeSupplyArea(SupplyAreaInterface $supplyArea): self
    {
        $this->supplyAreas->removeElement($supplyArea);

        return $this;
    }

    public function getSupplyAreas(): \Doctrine\Common\Collections\Collection
    {
        return $this->supplyAreas;
    }

    public function addHospital(HospitalInterface $hospital): self
    {
        if (!$this->hospitals->contains($hospital)) {
            $this->hospitals[] = $hospital;

            $hospital->setState($this);
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
