<?php

namespace App\Domain\Command\Import;

use App\Domain\Contracts\ImportInterface;

class ImportDataCommand
{
    private ImportInterface $import;

    public function __construct(ImportInterface $import)
    {
        $this->import = $import;
    }

    public function getImport(): ImportInterface
    {
        return $this->import;
    }
}
