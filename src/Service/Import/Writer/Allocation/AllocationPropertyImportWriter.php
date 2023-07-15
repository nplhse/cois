<?php

declare(strict_types=1);

namespace App\Service\Import\Writer\Allocation;

use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationPropertyImportWriter implements \App\Application\Contract\AllocationImportWriterInterface
{
    private array $simpleParameters = [
        ['key' => 'Schockraum', 'target' => 'RequiresResus', 'pattern' => 'S+'],
        ['key' => 'Herzkatheter', 'target' => 'RequiresCathlab', 'pattern' => 'H+'],
        ['key' => 'Reanimation', 'target' => 'isCPR', 'pattern' => 'R+'],
        ['key' => 'Beatmet', 'target' => 'isVentilated', 'pattern' => 'B+'],
        ['key' => 'Arztbegleitet', 'target' => 'isWithPhysician', 'pattern' => 'N+'],
        ['key' => 'Schock', 'target' => 'isShock', 'pattern' => 'Schock'],
        ['key' => 'Schwanger', 'target' => 'isPregnant', 'pattern' => 'Schwanger'],
        ['key' => 'Arbeits-/Wege-/Schulunfall', 'target' => 'isWorkAccident', 'pattern' => 'BG+'],
        ['key' => 'Fachbereich war abgemeldet?', 'target' => 'SpecialityWasClosed', 'pattern' => 'Ja'],
    ];

    private array $directParameters = [
        ['key' => 'Ansteckungsfähig', 'target' => 'isInfectious'],
        ['key' => 'Ansteckungsf?hig', 'target' => 'isInfectious'],
        ['key' => 'Grund', 'target' => 'Assignment'],
        ['key' => 'Anlass', 'target' => 'Occasion'],
        ['key' => 'Fachgebiet', 'target' => 'Speciality'],
        ['key' => 'Fachbereich', 'target' => 'SpecialityDetail'],
        ['key' => 'PZC-Text', 'target' => 'Indication'],
        ['key' => 'Transportmittel', 'target' => 'ModeOfTransport'],
        ['key' => 'Neben-PZC-Text', 'target' => 'SecondaryIndication'],
        ['key' => 'Patienten-Übergabepunkt (PÜP)', 'target' => 'HandoverPoint'],
        ['key' => 'Patienten-Übergabepunkt (PüP)', 'target' => 'HandoverPoint'],
        ['key' => 'Patienten-?bergabepunkt (P?P)', 'target' => 'HandoverPoint'],
    ];

    public function process(?object $entity, array $row, ImportInterface $import): ?object
    {
        if (!$entity instanceof Allocation) {
            return $entity;
        }

        $entity = $this->setTimes($entity, $row);
        $entity = $this->setDemographicData($entity, $row);
        $entity = $this->setSimpleParameters($entity, $row);
        $entity = $this->setDirectParameters($entity, $row);

        return $entity;
    }

    public function setTimes(Allocation $allocation, array $row): Allocation
    {
        $format = 'd.m.Y H:i:s';

        if (8 === strlen($row['Datum (Erstellungsdatum)'])) {
            $format = 'd.m.y H:i:s';
        }

        $allocation->setCreatedAt(\DateTime::createFromFormat($format, $row['Datum (Erstellungsdatum)'].' '.$row['Uhrzeit (Erstellungsdatum)'], new \DateTimeZone('Europe/Berlin')));
        $allocation->setArrivalAt(\DateTime::createFromFormat($format, $row['Datum (Eintreffzeit)'].' '.$row['Uhrzeit (Eintreffzeit)'], new \DateTimeZone('Europe/Berlin')));

        return $allocation;
    }

    public function setDemographicData(Allocation $allocation, array $row): Allocation
    {
        $allocation->setGender($row['Geschlecht']);
        $allocation->setAge((int) $row['Alter']);

        return $allocation;
    }

    public function setSimpleParameters(Allocation $allocation, array $row): Allocation
    {
        foreach ($this->simpleParameters as $parameter) {
            if (array_key_exists($parameter['key'], $row)) {
                if (isset($parameter['key'])) {
                    if ($parameter['pattern'] === $row[$parameter['key']]) {
                        $value = true;
                    } else {
                        $value = false;
                    }

                    $allocation->{'set'.$parameter['target']}($value);
                }
            }
        }

        return $allocation;
    }

    public function setDirectParameters(Allocation $allocation, array $row): Allocation
    {
        foreach ($this->directParameters as $parameter) {
            if (array_key_exists($parameter['key'], $row)) {
                $allocation->{'set'.$parameter['target']}($row[$parameter['key']]);
            }
        }

        return $allocation;
    }
}
