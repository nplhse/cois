<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class UrgencyStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $urgency = 'No SK',
        private int $counter = 0
    ) {
    }

    public function getUrgency(): string
    {
        return $this->urgency;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
