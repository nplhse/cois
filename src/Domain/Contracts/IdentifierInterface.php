<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface IdentifierInterface
{
    public function setId(int $id): self;

    public function getId(): int;
}
