<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

class ChangePasswordCommand
{
    public function __construct(
        private int $id,
        private string $plainPassword
    ) {
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
