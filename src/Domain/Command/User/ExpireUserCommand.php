<?php

namespace App\Domain\Command\User;

class ExpireUserCommand
{
    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
