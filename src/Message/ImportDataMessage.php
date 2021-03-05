<?php

namespace App\Message;

use App\Entity\Hospital;
use App\Entity\Import;

final class ImportDataMessage
{
    private Import $import;

    private Hospital $hospital;

    private bool $cli;

    public function __construct(Import $import, Hospital $hospital, bool $cli = false)
    {
        $this->import = $import;
        $this->hospital = $hospital;
        $this->cli = $cli;
    }

    public function getImport(): Import
    {
        return $this->import;
    }

    public function getHospital(): Hospital
    {
        return $this->hospital;
    }

    public function getCli(): bool
    {
        return $this->cli;
    }
}
