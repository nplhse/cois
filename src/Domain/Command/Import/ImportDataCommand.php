<?php

namespace App\Domain\Command\Import;

class ImportDataCommand
{
    private int $importId;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    public function getImportId(): int
    {
        return $this->importId;
    }
}
