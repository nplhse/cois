<?php

declare(strict_types=1);

namespace App\Domain\Command\Import;

class ImportDataCommand
{
    public function __construct(
        private int $importId
    ) {
    }

    public function getImportId(): int
    {
        return $this->importId;
    }
}
