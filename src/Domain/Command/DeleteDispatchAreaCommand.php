<?php

namespace App\Domain\Command;

use App\Domain\Contracts\StateInterface;

class DeleteDispatchAreaCommand
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
