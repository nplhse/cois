<?php

declare(strict_types=1);

namespace Domain\Contracts;

use Doctrine\Common\Collections\Collection;

interface StateInterface
{
    public function getId(): int;

    public function setName(string $name): self;

    public function getName(): string;

    public function getDispatchAreas(): Collection;

    public function addDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function removeDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function getSupplyAreas(): Collection;

    public function addSupplyArea(SupplyAreaInterface $supplyArea): self;

    public function removeSupplyArea(SupplyAreaInterface $supplyArea): self;
}
