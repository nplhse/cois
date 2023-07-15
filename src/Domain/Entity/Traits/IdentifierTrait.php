<?php

declare(strict_types=1);

namespace Domain\Entity\Traits;

trait IdentifierTrait
{
    protected int $id;

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
