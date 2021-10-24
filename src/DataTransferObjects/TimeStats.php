<?php

namespace App\DataTransferObjects;

class TimeStats implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private int $time;

    private int $counter;

    public function __construct(int $time, int $counter)
    {
        $this->time = $time;
        $this->counter = $counter;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
