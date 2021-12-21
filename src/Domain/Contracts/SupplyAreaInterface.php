<?php

namespace App\Domain\Contracts;

interface SupplyAreaInterface
{
    public function getId(): int;

    public function setName(string $name): self;

    public function getName(): string;
}
