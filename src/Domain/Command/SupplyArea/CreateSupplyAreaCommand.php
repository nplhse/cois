<?php

namespace App\Domain\Command\SupplyArea;

class CreateSupplyAreaCommand
{
    public function __construct(
        private string $name,
        private int $stateId
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStateId(): int
    {
        return $this->stateId;
    }
}
