<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class InfectionStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $infection;

    private int $counter;

    final public function __construct(string $infection, int $counter = 0)
    {
        $this->infection = $infection;
        $this->counter = $counter;
    }

    public function getInfection(): string
    {
        return $this->infection;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
