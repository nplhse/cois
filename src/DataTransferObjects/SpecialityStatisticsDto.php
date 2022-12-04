<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class SpecialityStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $speciality,
        private int $counter = 0
    ) {
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
