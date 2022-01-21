<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Adapter\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface StateInterface
{
    public function getId(): int;

    public function setName(string $name): self;

    public function getName(): string;

    public function getDispatchAreas(): Collection;

    public function addDispatchArea(DispatchAreaInterface $dispatchArea): self;

    public function removeDispatchArea(DispatchAreaInterface $dispatchArea): self;
}
