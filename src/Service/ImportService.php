<?php

namespace App\Service;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use Doctrine\ORM\EntityManagerInterface;
use ForceUTF8\Encoding;

class ImportService
{
    private string $path;

    private array $result = [];

    private EntityManagerInterface $em;

    private bool $hasFieldNames;

    private string $delimiter;

    private string $enclosure;

    public function __construct(EntityManagerInterface $entityManager, bool $hasFieldNames = true, string $delimiter = ';', string $enclosure = '"')
    {
        $this->em = $entityManager;

        $this->hasFieldNames = $hasFieldNames;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function importFromCSV(string $path): bool
    {
        if (file_exists($path)) {
            $this->path = $path;
            $file = fopen($path, 'r');
        } else {
            throw new \Exception('Could not open file: '.$path);
        }

        if ($this->hasFieldNames) {
            $keys = fgetcsv($file, null, $this->delimiter, $this->enclosure);
        } else {
            $keys = [];
        }

        while ($row = fgetcsv($file, null, $this->delimiter, $this->enclosure)) {
            $n = count($row);
            $res = [];
            for ($i = 0; $i < $n; ++$i) {
                $idx = ($this->hasFieldNames) ? $keys[$i] : $i;
                $id8 = Encoding::fixUTF8($idx);
                $val = Encoding::fixUTF8($row[$i]);
                $res[$id8] = $val;
            }
            $this->result[] = $res;
        }
        fclose($file);

        return true;
    }

    public function processToAllocation(Import $import, Hospital $hospital): bool
    {
        $import->setStatus('processing');

        $time = null;
        $count = 0;

        try {
            $time_start = microtime(true);

            foreach ($this->result as $row) {
                ++$count;

                $allocation = new Allocation();
                $allocation->setImport($import);
                $allocation->setHospital($hospital);

                $allocation->setDispatchArea($row['Versorgungsbereich']);
                $allocation->setSupplyArea($row['KHS-Versorgungsgebiet']);
                $allocation->setCreatedAt(new \DateTime($row['Erstellungsdatum']));
                $allocation->setCreationDate($row['Datum (Erstellungsdatum)']);
                $allocation->setCreationTime($row['Uhrzeit (Erstellungsdatum)']);
                $allocation->setCreationDay($row['Tag (Erstellungsdatum)']);
                $allocation->setCreationWeekday($row['Wochentag (Erstellungsdatum)']);
                $allocation->setCreationMonth($row['Monat (Erstellungsdatum)']);
                $allocation->setCreationYear($row['Jahr (Erstellungsdatum)']);
                $allocation->setCreationHour($row['Stunde (Erstellungsdatum)']);
                $allocation->setCreationMinute($row['Minute (Erstellungsdatum)']);
                $allocation->setArrivalAt(new \DateTime($row['Datum (Eintreffzeit)'].' '.$row['Uhrzeit (Eintreffzeit)']));
                $allocation->setArrivalDate($row['Datum (Eintreffzeit)']);
                $allocation->setArrivalTime($row['Uhrzeit (Eintreffzeit)']);
                $allocation->setArrivalDay($row['Tag (Eintreffzeit)']);
                $allocation->setArrivalWeekday($row['Wochentag (Eintreffzeit)']);
                $allocation->setArrivalMonth($row['Monat (Eintreffzeit)']);
                $allocation->setArrivalYear($row['Jahr (Eintreffzeit)']);
                $allocation->setArrivalHour($row['Stunde (Eintreffzeit)']);
                $allocation->setArrivalMinute($row['Minute (Eintreffzeit)']);
                if ('S+' == $row['Schockraum']) {
                    $allocation->setRequiresResus(true);
                } else {
                    $allocation->setRequiresResus(false);
                }
                if ('H+' == $row['Herzkatheter']) {
                    $allocation->setRequiresCathlab(true);
                } else {
                    $allocation->setRequiresCathlab(false);
                }
                $allocation->setOccasion($row['Anlass']);
                $allocation->setGender($row['Geschlecht']);
                $allocation->setAge((int) $row['Alter']);
                if ('R+' == $row['Reanimation']) {
                    $allocation->setIsCPR(true);
                } else {
                    $allocation->setIsCPR(false);
                }
                if ('B+' == $row['Beatmet']) {
                    $allocation->setIsVentilated(true);
                } else {
                    $allocation->setIsVentilated(false);
                }
                $allocation->setIsShock($row['Schock']);
                if (isset($row['Ansteckungsf?hig'])) {
                    $allocation->setIsInfectious($row['Ansteckungsf?hig']);
                } else {
                    $allocation->setIsInfectious($row['Ansteckungsfähig']);
                }
                if ('Schwanger' == $row['Schwanger']) {
                    $allocation->setIsPregnant(true);
                } else {
                    $allocation->setIsPregnant(false);
                }
                if ('N+' == $row['Arztbegleitet']) {
                    $allocation->setIsWithPhysician(true);
                } else {
                    $allocation->setIsWithPhysician(false);
                }
                if ('BG+' == $row['Arbeits-/Wege-/Schulunfall']) {
                    $allocation->setIsWorkAccident(true);
                } else {
                    $allocation->setIsWorkAccident(false);
                }
                $allocation->setAssignment($row['Grund']);
                $allocation->setModeOfTransport($row['Transportmittel']);
                $allocation->setComment($row['Freitext']);
                $allocation->setSpeciality($row['Fachgebiet']);
                $allocation->setSpecialityDetail($row['Fachbereich']);
                if (isset($row['Patienten-?bergabepunkt (P?P)'])) {
                    $allocation->setHandoverPoint($row['Patienten-?bergabepunkt (P?P)']);
                } else {
                    if (isset($row['Patienten-Übergabepunkt (PÜP)'])) {
                        $allocation->setHandoverPoint($row['Patienten-Übergabepunkt (PÜP)']);
                    }
                    $allocation->setHandoverPoint('');
                }
                if ('Nein' == $row['Fachbereich war abgemeldet?']) {
                    $allocation->setSpecialityWasClosed(false);
                } else {
                    $allocation->setSpecialityWasClosed(true);
                }
                $allocation->setPZC((int) $row['PZC']);
                $allocation->setPZCText($row['PZC-Text']);
                $allocation->setSecondaryPZC(null);
                $allocation->setSecondaryPZCText($row['Neben-PZC-Text']);
                $allocation->setRMI($allocation->getRMI());
                $allocation->setSK($allocation->getSK());

                $this->em->persist($allocation);
            }

            $time_end = microtime(true);
            $time = $time_end - $time_start;
        } catch (\Exception $e) {
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            $import->setStatus('failed');
            $import->setLastError($e->getMessage());
            $import->setLastRun(new \DateTime('NOW'));
            $import->setDuration($time);
            ($count) ? $import->setItemCount($count) : null;

            $this->em->persist($import);
            $this->em->flush();

            return false;
        }

        $import->setDuration($time);
        $import->setItemCount($count);
        $import->setTimesRun($import->getTimesRun() + 1);
        $import->setLastRun(new \DateTime('NOW'));
        $import->setLastError(null);
        $import->setStatus('finished');

        $this->em->persist($import);
        $this->em->flush();

        return true;
    }
}
