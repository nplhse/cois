<?php

namespace App\Service\Import\Writer;

use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationBaseWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 100;
    }

    public function processData(?object $entity, array $row, ImportInterface $import): ?object
    {
        if (!$entity) {
            /** @var Allocation $entity */
            $entity = new Allocation();
        }

        $entity->setImport($import);
        $entity->setState($import->getHospital()->getState());
        $entity->setDispatchArea($import->getHospital()->getDispatchArea());
        $entity->setSupplyArea($import->getHospital()->getSupplyArea());
        $entity->setHospital($import->getHospital());

        $entity->setGender($row['Geschlecht']);
        $entity->setAge((int) $row['Alter']);

        $entity->setCreatedAt(new \DateTime($row['Erstellungsdatum']));
        $entity->setArrivalAt(new \DateTime($row['Datum (Eintreffzeit)'].' '.$row['Uhrzeit (Eintreffzeit)']));

        return $entity;
    }
}
