<?php

namespace App\DataTransferObjects;

class AgeStatistics
{
    private array $ages = [];

    private int $max_age;

    public function setAges(array $ages): void
    {
        $this->ages = $ages;
    }

    public function getAges(): array
    {
        return $this->ages;
    }

    public function setAge(int $age, int $count): void
    {
        $this->ages[$age] = $count;
    }

    public function getAge(int $age): int
    {
        if (!isset($this->ages[$age])) {
            return 0;
        }

        return (int) $this->ages[$age];
    }

    public function getMaxAge(): int
    {
        return $this->max_age;
    }

    public function setMaxAge(int $max_age): void
    {
        $this->max_age = $max_age;
    }
}
