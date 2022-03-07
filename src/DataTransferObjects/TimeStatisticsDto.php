<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class TimeStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private int $time;

    private int $counter;

    final public function __construct(int $time, int $counter)
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
