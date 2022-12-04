<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class TransportStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private string $transport,
        private int $counter = 0
    ) {
    }

    public function getTransport(): string
    {
        return $this->transport;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
