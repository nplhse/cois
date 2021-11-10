<?php

namespace App\DataTransferObjects;

class PropertyStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $property;

    private int $counter;

    final public function __construct(string $property, int $counter = 0)
    {
        $this->property = $property;
        $this->counter = $counter;
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
