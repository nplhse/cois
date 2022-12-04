<?php

namespace App\Domain\Command\User;

class PromoteUserCommand
{
    public function __construct(
        private int $id,
        private bool $isVerified,
        private bool $isParticipant
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function getIsParticipant(): bool
    {
        return $this->isParticipant;
    }
}
