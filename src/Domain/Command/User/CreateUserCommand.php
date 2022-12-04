<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

class CreateUserCommand
{
    public function __construct(
        private string $username,
        private string $email,
        private string $plainPassword,
        private array $roles,
        private bool $hasCredentialsExpired,
        private bool $isVerified,
        private bool $isParticipant
    ) {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasCredentialsExpired(): bool
    {
        return $this->hasCredentialsExpired;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function isParticipant(): bool
    {
        return $this->isParticipant;
    }
}
