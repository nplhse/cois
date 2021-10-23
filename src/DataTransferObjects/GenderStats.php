<?php

namespace App\DataTransferObjects;

class GenderStats implements ConstructFromArrayInterface
{
    use ConstructableFromArrayTrait;

    private string $gender;

    private int $counter;

    public function __construct(string $gender, int $counter)
    {
        $this->gender = $gender;
        $this->counter = $counter;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}
