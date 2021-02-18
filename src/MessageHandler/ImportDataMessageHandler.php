<?php

namespace App\MessageHandler;

use App\Entity\Allocation;
use App\Message\ImportDataMessage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use ForceUTF8\Encoding;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportDataMessageHandler implements MessageHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(ImportDataMessage $message): void
    {
        $import = $message->getImport();
        $path = '../var/storage/import/'.$import->getPath();
        $hospital = $message->getHospital();

        $result = $this->arrayFromCSV($path, true);

        foreach ($result as $row) {
            $allocation = new Allocation();
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
            $allocation->setRequiresResus($row['Schockraum']);
            $allocation->setRequiresCathlab($row['Herzkatheter']);
            $allocation->setOccasion($row['Anlass']);
            $allocation->setGender($row['Geschlecht']);
            $allocation->setAge($row['Alter']);
            $allocation->setIsCPR($row['Reanimation']);
            $allocation->setIsVentilated($row['Beatmet']);
            $allocation->setIsShock($row['Schock']);
            if (isset($row['Ansteckungsf?hig'])) {
                $allocation->setIsInfectious($row['Ansteckungsf?hig']);
            } else {
                $allocation->setIsInfectious($row['Ansteckungsfähig']);
            }
            $allocation->setIsPregnant($row['Schwanger']);
            $allocation->setIsWithPhysician($row['Arztbegleitet']);
            $allocation->setAssignment($row['Grund']);
            $allocation->setModeOfTransport($row['Transportmittel']);
            $allocation->setComment($row['Freitext']);
            $allocation->setSpeciality($row['Fachgebiet']);
            $allocation->setSpecialityDetail($row['Fachbereich']);
            if (isset($row['Patienten-?bergabepunkt (P?P)'])) {
                $allocation->setHandoverPoint($row['Patienten-?bergabepunkt (P?P)']);
            } else {
                $allocation->setHandoverPoint("");
            }
            $allocation->setSpecialityWasClosed($row['Fachbereich war abgemeldet?']);
            $allocation->setPZC((int) $row['PZC']);
            $allocation->setPZCText($row['PZC-Text']);
            $allocation->setSecondaryPZC(null);
            $allocation->setSecondaryPZCText($row['Neben-PZC-Text']);

            dump($allocation);

            $this->em->persist($allocation);
        }

        $this->em->flush();
    }

    private function arrayFromCSV($file, $hasFieldNames = false, $delimiter = ';', $enclosure = '"'): array
    {
        $result = [];
        $size = filesize($file) + 1;
        $file = fopen($file, 'r');

        //TO DO: There must be a better way of finding out the size of the longest row... until then
        if ($hasFieldNames) {
            $keys = fgetcsv($file, $size, $delimiter, $enclosure);
        }

        while ($row = fgetcsv($file, $size, $delimiter, $enclosure)) {
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
}
