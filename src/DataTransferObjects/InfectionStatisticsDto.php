<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class InfectionStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $infection,
        private int $counter = 0
    ) {
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
