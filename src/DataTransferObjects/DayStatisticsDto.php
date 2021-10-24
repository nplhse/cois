<?php

namespace App\DataTransferObjects;

class DayStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $day;

    private int $counter;

    final public function __construct(string $day, int $counter)
    {
        $this->day = $day;
        $this->counter = $counter;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
