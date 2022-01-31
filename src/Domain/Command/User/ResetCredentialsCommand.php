<?php

namespace App\Domain\Command\User;

class ResetCredentialsCommand
{
    private int $id;

    private string $plainPassword;

    public function __construct(int $id, string $plainPassword)
    {
        $this->id = $id;
        $this->plainPassword = $plainPassword;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
