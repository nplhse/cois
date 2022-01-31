<?php

namespace App\Domain\Command\User;

class ChangeEmailCommand
{
    private int $id;

    private string $email;

    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
