<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class GenderStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $gender,
        private int $counter
    ) {
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
