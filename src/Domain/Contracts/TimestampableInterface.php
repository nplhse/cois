<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface TimestampableInterface
{
    public function getCreatedAt(): \DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;
}
