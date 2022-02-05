<?php

namespace App\Domain\Command\User;

class PromoteUserCommand
{
    private int $id;

    private bool $isVerified;

    private bool $isParticipant;

    public function __construct(int $id, bool $isVerified, bool $isParticipant)
    {
        $this->id = $id;
        $this->isVerified = $isVerified;
        $this->isParticipant = $isParticipant;
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
