<?php

namespace App\DataTransferObjects;

class UrgencyStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $urgency;

    private int $counter;

    final public function __construct(string $urgency = 'No SK', int $counter = 0)
    {
        $this->urgency = $urgency;
        $this->counter = $counter;
    }

    public function getUrgency(): string
    {
        return $this->urgency;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
