<?php

namespace App\Domain\Command\User;

class CreateUserCommand
{
    private string $username;

    private string $email;

    private string $plainPassword;

    private array $roles;

    private bool $hasCredentialsExpired;

    private bool $isVerified;

    private bool $isParticipant;

    public function __construct(string $username, string $email, string $plainPassword, array $roles, bool $hasCredentialsExpired, bool $isVerified, bool $isParticipant)
    {
        $this->username = $username;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->roles = $roles;
        $this->hasCredentialsExpired = $hasCredentialsExpired;
        $this->isVerified = $isVerified;
        $this->isParticipant = $isParticipant;
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
