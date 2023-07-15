<?php

declare(strict_types=1);

namespace Domain\Command\User;

class EditUserCommand
{
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private ?string $plainPassword,
        private array $roles
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
