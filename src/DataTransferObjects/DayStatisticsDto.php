<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class DayStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $day,
        private int $counter
    ) {
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
