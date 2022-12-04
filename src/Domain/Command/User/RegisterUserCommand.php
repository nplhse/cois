<?php

namespace App\Domain\Command\User;

class RegisterUserCommand
{
    public function __construct(
        private string $username,
        private string $email,
        private string $plainPassword
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
}
