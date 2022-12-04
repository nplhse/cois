<?php

namespace App\Domain\Command\State;

class CreateStateCommand
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
