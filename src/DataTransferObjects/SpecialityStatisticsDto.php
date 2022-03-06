<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class SpecialityStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $speciality;

    private int $counter;

    final public function __construct(string $speciality, int $counter = 0)
    {
        $this->speciality = $speciality;
        $this->counter = $counter;
    }

    public function getSpeciality(): string
    {
        return $this->speciality;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
