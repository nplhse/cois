<?php

namespace App\Domain\Command;

use App\Domain\Contracts\StateInterface;

class CreateDispatchAreaCommand
{
    private string $name;

    private StateInterface $state;

    public function __construct(string $name, StateInterface $state)
    {
        $this->name = $name;
        $this->state = $state;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
