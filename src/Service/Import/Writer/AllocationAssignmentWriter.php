<?php

namespace App\Service\Import\Writer;

use App\Domain\Contracts\ImportInterface;

class AllocationAssignmentWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 45;
    }

    public function processData(?object $entity, array $row, ImportInterface $import): ?object
    {
        $entity->setUrgency((int) substr($row['PZC'], 5, 1));
        $entity->setAssignment($row['Grund']);
        $entity->setOccasion($row['Anlass']);

        $entity->setModeOfTransport($row['Transportmittel']);
        $entity->setComment($row['Freitext']);

        $entity->setSpeciality($row['Fachgebiet']);
        $entity->setSpecialityDetail($row['Fachbereich']);

        if (isset($row['Patienten-?bergabepunkt (P?P)'])) {
            $entity->setHandoverPoint($row['Patienten-?bergabepunkt (P?P)']);
        } else {
            if (isset($row['Patienten-Übergabepunkt (PÜP)'])) {
                $entity->setHandoverPoint($row['Patienten-Übergabepunkt (PÜP)']);
            }
            $entity->setHandoverPoint('');
        }
        if ('Nein' == $row['Fachbereich war abgemeldet?']) {
            $entity->setSpecialityWasClosed(false);
        } else {
            $entity->setSpecialityWasClosed(true);
        }

        $entity->setIndication($row['PZC-Text']);
        $entity->setIndicationCode((int) substr($row['PZC'], 0, 3));
        $entity->setSecondaryIndication(null);
        $entity->setSecondaryIndicationCode(null);

        return $entity;
    }
}
