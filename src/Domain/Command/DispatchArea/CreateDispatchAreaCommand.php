<?php

namespace App\Domain\Command\DispatchArea;

use App\Domain\Contracts\StateInterface;

class CreateDispatchAreaCommand
{
    public function __construct(
        private string $name,
        private StateInterface $state
    ) {
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
