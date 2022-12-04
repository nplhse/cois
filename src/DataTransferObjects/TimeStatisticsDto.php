<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class TimeStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private int $time,
        private int $counter
    ) {
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
