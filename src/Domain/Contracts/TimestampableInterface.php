<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface TimestampableInterface
{
    public function getCreatedAt(): \DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;
}
