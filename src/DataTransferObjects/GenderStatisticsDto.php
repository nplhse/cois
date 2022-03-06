<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class GenderStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $gender;

    private int $counter;

    final public function __construct(string $gender, int $counter)
    {
        $this->gender = $gender;
        $this->counter = $counter;
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
