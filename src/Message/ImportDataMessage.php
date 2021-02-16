<?php

namespace App\Message;

use App\Entity\Hospital;
use App\Entity\Import;
use App\Entity\User;
use App\Repository\HospitalRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final class ImportDataMessage
{
    private Import $import;

    private Hospital $hospital;

    public function __construct(Import $import, Hospital $hospital)
    {
        $this->import = $import;
        $this->hospital = $hospital;
    }

    public function getImport(): Import
    {
        return $this->import;
    }

    public function getHospital(): Hospital
    {
        return $this->hospital;
    }
}
