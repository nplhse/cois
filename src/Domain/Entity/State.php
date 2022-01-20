<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contracts\StateInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class State implements StateInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    protected string $name;

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
}
