<?php

declare(strict_types=1);

namespace Domain\Command\Import;

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
