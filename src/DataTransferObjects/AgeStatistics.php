<?php

namespace App\DataTransferObjects;

class AgeStatistics
{
    private array $ages = [];

    private array $maleAges = [];

    private array $femaleAges = [];

    private array $otherAges = [];

    private ?int $maxAge = null;

    private ?int $maxMaleAge = null;

    private ?int $maxFemaleAge = null;

    private ?int $maxOtherAge = null;

    public function setAges(array $ages): void
    {
        $this->ages = $ages;
    }

    public function getAges(): array
    {
        return $this->ages;
    }

    public function getAge(int $age): int
    {
        if (!isset($this->ages[$age])) {
            return 0;
        }

        return (int) $this->ages[$age];
    }

    public function getMaxAge(): ?int
    {
        if (!isset($this->maxAge)) {
            $this->maxAge = array_key_last($this->ages);
        }

        return $this->maxAge;
    }

    public function setMaleAges(array $ages): void
    {
        $this->maleAges = $ages;
    }

    public function getMaleAges(): array
    {
        return $this->maleAges;
    }

    public function getMaleAge(int $age): int
    {
        if (!isset($this->maleAges[$age])) {
            return 0;
        }

        return (int) $this->maleAges[$age];
    }

    public function getMaleMaxAge(): ?int
    {
        if (!isset($this->maxMaleAge)) {
            $this->maxMaleAge = array_key_last($this->maleAges);
        }

        return $this->maxMaleAge;
    }

    public function setFemaleAges(array $ages): void
    {
        $this->femaleAges = $ages;
    }

    public function getFemaleAges(): array
    {
        return $this->femaleAges;
    }

    public function getFemaleAge(int $age): int
    {
        if (!isset($this->femaleAges[$age])) {
            return 0;
        }

        return (int) $this->femaleAges[$age];
    }

    public function getFemaleMaxAge(): ?int
    {
        if (!isset($this->maxFemaleAge)) {
            $this->maxFemaleAge = array_key_last($this->femaleAges);
        }

        return $this->maxFemaleAge;
    }

    public function setOtherAges(array $ages): void
    {
        $this->otherAges = $ages;
    }

    public function getOtherAges(): array
    {
        return $this->otherAges;
    }

    public function getOtherAge(int $age): int
    {
        if (!isset($this->otherAges[$age])) {
            return 0;
        }

        return (int) $this->otherAges[$age];
    }

    public function getOthereMaxAge(): ?int
    {
        if (!isset($this->maxOtherAge)) {
            $this->maxOtherAge = array_key_last($this->otherAges);
        }

        return $this->maxOtherAge;
    }
}
