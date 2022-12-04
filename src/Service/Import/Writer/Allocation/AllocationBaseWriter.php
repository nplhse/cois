<?php

declare(strict_types=1);

namespace App\Service\Import\Writer\Allocation;

use App\Application\Exception\ImportWriteException;
use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationBaseWriter implements \App\Application\Contract\AllocationImportWriterInterface
{
    public function process(?object $entity, array $row, ImportInterface $import): ?object
    {
        if ($this->checkIfRowIsFromMANV($row)) {
            throw new ImportWriteException('This row is related to a MANV, which is not currently supported.');
        }

        if (!$entity) {
            /** @var Allocation $entity */
            $entity = new Allocation();
        }

        return $this->setDataFromImport($entity, $row, $import);
    }

    private function checkIfRowIsFromMANV(array $row): bool
    {
        if (!empty($row['MANV']) || !empty($row['MANV-ID'])) {
            return true;
        }

        return false;
    }

    private function setDataFromImport(Allocation $allocation, array $row, ImportInterface $import): Allocation
    {
        $allocation->setImport($import);
        $allocation->setState($import->getHospital()->getState());
        $allocation->setDispatchArea($import->getHospital()->getDispatchArea());
        $allocation->setSupplyArea($import->getHospital()->getSupplyArea());
        $allocation->setHospital($import->getHospital());

        return $allocation;
    }
}
