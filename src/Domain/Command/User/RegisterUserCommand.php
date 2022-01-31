<?php

namespace App\Domain\Command\User;

class RegisterUserCommand
{
    private string $username;

    private string $email;

    private string $plainPassword;

    public function __construct(string $username, string $email, string $plainPassword)
    {
        $this->username = $username;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
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
