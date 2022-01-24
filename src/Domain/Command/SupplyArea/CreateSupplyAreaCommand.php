<?php

namespace App\Domain\Command\SupplyArea;

class CreateSupplyAreaCommand
{
    private string $name;

    private int $stateId;

    public function __construct(string $name, int $stateId)
    {
        $this->stateId = $stateId;
        $this->name = $name;
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
