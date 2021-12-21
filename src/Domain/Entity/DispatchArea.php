<?php

namespace App\Domain\Entity;

use App\Domain\Contracts\DispatchAreaInterface;
use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class DispatchArea implements DispatchAreaInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    private string $name;

    private StateInterface $state;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setState(StateInterface $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }
}
