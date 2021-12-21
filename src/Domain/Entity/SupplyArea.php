<?php

namespace App\Domain\Entity;

use App\Domain\Contracts\SupplyAreaInterface;
use App\Domain\Entity\Traits\IdentifierTrait;
use App\Domain\Entity\Traits\TimestampableTrait;

class SupplyArea implements SupplyAreaInterface, \Stringable
{
    use IdentifierTrait;
    use TimestampableTrait;

    private string $name;

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
