<?php

namespace App\Domain\Command\Import;

class DeleteImportCommand
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
