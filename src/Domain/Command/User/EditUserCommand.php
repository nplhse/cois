<?php

namespace App\Domain\Command\User;

class EditUserCommand
{
    private string $id;

    private string $username;

    private string $email;

    private ?string $plainPassword = null;

    private array $roles;

    public function __construct(int $id, string $username, string $email, ?string $plainPassword, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->roles = $roles;
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
