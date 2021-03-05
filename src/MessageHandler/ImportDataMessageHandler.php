<?php

namespace App\MessageHandler;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use App\Message\ImportDataMessage;
use Doctrine\ORM\EntityManagerInterface;
use ForceUTF8\Encoding;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportDataMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(ImportDataMessage $message): void
    {
        $import = $message->getImport();
        $hospital = $message->getHospital();

        if ($message->getCli()) {
            $path = 'var/storage/import/'.$import->getPath();
        } else {
            $path = '../var/storage/import/'.$import->getPath();
        }

        $result = $this->arrayFromCSV($path, true);

        $this->processResult($result, $import, $hospital);
    }

    private function arrayFromCSV(string $file, bool $hasFieldNames = false, string $delimiter = ';', string $enclosure = '"'): array
    {
        $result = [];
        //$size = filesize($file) + 1;

        if (file_exists($file)) {
            $file = fopen($file, 'r');
        } else {
            throw new FileNotFoundException($file);
        }

        //TO DO: There must be a better way of finding out the size of the longest row... until then
        if ($hasFieldNames) {
            $keys = fgetcsv($file, null, $delimiter, $enclosure);
        }

        while ($row = fgetcsv($file, null, $delimiter, $enclosure)) {
            $n = count($row);
            $res = [];
            for ($i = 0; $i < $n; ++$i) {
                $idx = ($hasFieldNames) ? $keys[$i] : $i;
                $id8 = Encoding::fixUTF8($idx);
                $val = Encoding::fixUTF8($row[$i]);
                $res[$id8] = $val;
            }
            $result[] = $res;
        }
        fclose($file);

        return $result;
    }

    private function processResult(array $result, Import $import, Hospital $hospital): bool
    {
        foreach ($result as $row) {
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
            $allocation->setAge($row['Alter']);
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

            $this->em->persist($allocation);
        }

        $this->em->flush();

        return true;
    }
}
