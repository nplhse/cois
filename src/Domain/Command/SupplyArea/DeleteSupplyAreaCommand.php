<?php

namespace App\Domain\Command\SupplyArea;

class DeleteSupplyAreaCommand
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
