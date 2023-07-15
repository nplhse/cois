<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class AssignmentStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $assignment,
        private int $counter = 0
    ) {
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
