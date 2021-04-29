<?php

namespace App\DataTransferObjects;

class GenderStatistics
{
    private int $male_count = 0;

    private float $male_percent = 0;

    private int $female_count = 0;

    private float $female_percent = 0;

    private int $other_count = 0;

    private float $other_percent = 0;

    public function getMaleCount(): int
    {
        return $this->male_count;
    }

    public function setMaleCount(int $male_count): void
    {
        $this->male_count = $male_count;
    }

    public function getFemaleCount(): int
    {
        return $this->female_count;
    }

    public function setFemaleCount(int $female_count): void
    {
        $this->female_count = $female_count;
    }

    public function getOtherCount(): int
    {
        return $this->other_count;
    }

    public function setOtherCount(int $other_count): void
    {
        $this->other_count = $other_count;
    }

    public function getMalePercent(): float
    {
        return $this->male_percent;
    }

    public function setMalePercent(float $male_percent): void
    {
        $this->male_percent = $male_percent;
    }

    public function getFemalePercent(): float
    {
        return $this->female_percent;
    }

    public function setFemalePercent(float $female_percent): void
    {
        $this->female_percent = $female_percent;
    }

    public function getOtherPercent(): float
    {
        return $this->other_percent;
    }

    public function setOtherPercent(float $other_percent): void
    {
        $this->other_percent = $other_percent;
    }
}
