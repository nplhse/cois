<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class OccasionStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $occasion,
        private int $counter = 0
    ) {
    }

    public function getOccasion(): string
    {
        return $this->occasion;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
