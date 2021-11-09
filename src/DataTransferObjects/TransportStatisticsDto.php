<?php

namespace App\DataTransferObjects;

class TransportStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $transport;

    private int $counter;

    final public function __construct(string $transport, int $counter = 0)
    {
        $this->transport = $transport;
        $this->counter = $counter;
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
