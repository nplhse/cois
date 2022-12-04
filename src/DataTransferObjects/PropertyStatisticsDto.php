<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class PropertyStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $property,
        private int $counter = 0
    ) {
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
