<?php

declare(strict_types=1);

namespace Domain\Command\SupplyArea;

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
