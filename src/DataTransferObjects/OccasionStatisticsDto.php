<?php

namespace App\DataTransferObjects;

class OccasionStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $occasion;

    private int $counter;

    final public function __construct(string $occasion, int $counter = 0)
    {
        $this->occasion = $occasion;
        $this->counter = $counter;
    }

    public function getOccasion(): string
    {
        return $this->occasion;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
