<?php

declare(strict_types=1);

namespace Domain\Command\Import;

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
