<?php

declare(strict_types=1);

namespace Domain\Command\DispatchArea;

use Domain\Contracts\StateInterface;

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
