<?php

namespace App\DataTransferObjects;

class PZCStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private int $pzc;

    private string $pzcText;

    private int $counter;

    final public function __construct(int $PZC = 0, int $counter = 0, string $PZCText = '')
    {
        $this->pzc = $PZC;
        $this->pzcText = $PZCText;
        $this->counter = $counter;
    }

    public function getPZC(): int
    {
        return $this->pzc;
    }

    public function getPZCText(): string
    {
        return $this->pzcText;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
