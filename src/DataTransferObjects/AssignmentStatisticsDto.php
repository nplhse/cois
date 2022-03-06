<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class AssignmentStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $assignment;

    private int $counter;

    final public function __construct(string $assignment, int $counter = 0)
    {
        $this->assignment = $assignment;
        $this->counter = $counter;
    }

    public function getAssignment(): string
    {
        return $this->assignment;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
