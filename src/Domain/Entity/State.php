<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Adapter\ArrayCollection;
use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class State implements StateInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    protected string $name;

    protected \Doctrine\Common\Collections\Collection $dispatchAreas;

    protected \Doctrine\Common\Collections\Collection $supplyAreas;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
        $this->dispatchAreas = new ArrayCollection();
        $this->supplyAreas = new ArrayCollection();
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
}
