<?php

namespace App\Domain\Entity\Traits;

trait IdentifierTrait
{
    private int $id;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
