<?php

namespace App\DataTransferObjects;

use App\Application\Contract\ConstructFromArrayInterface;
use App\Application\Traits\ConstructableFromArrayTrait;

class PZCStatisticsDto implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    final public function __construct(
        private int $pzc = 0,
        private int $counter = 0,
        private string $pzcText = ''
    ) {
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
