<?php

namespace App\Service\Import\Writer;

use App\Domain\Contracts\ImportInterface;

class AllocationPropertyWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 50;
    }

    public function processData(?object $entity, array $row, ImportInterface $import): object
    {
        if ('S+' === $row['Schockraum']) {
            $entity->setRequiresResus(true);
        } elseif ('S-' === $row['Schockraum']) {
            $entity->setRequiresResus(false);
        } elseif ('' === $row['Schockraum']) {
            $entity->setRequiresResus(false);
        } else {
            $entity->setRequiresResus(true);
        }
        if ('H+' === $row['Herzkatheter']) {
            $entity->setRequiresCathlab(true);
        } else {
            $entity->setRequiresCathlab(false);
        }
        if ('R+' === $row['Reanimation']) {
            $entity->setIsCPR(true);
        } else {
            $entity->setIsCPR(false);
        }
        if ('B+' === $row['Beatmet']) {
            $entity->setIsVentilated(true);
        } else {
            $entity->setIsVentilated(false);
        }
        $entity->setIsShock($row['Schock']);
        if (isset($row['Ansteckungsf?hig'])) {
            $entity->setIsInfectious($row['Ansteckungsf?hig']);
        } else {
            $entity->setIsInfectious($row['Ansteckungsfähig']);
        }
        if ('Schwanger' === $row['Schwanger']) {
            $entity->setIsPregnant(true);
        } else {
            $entity->setIsPregnant(false);
        }
        if ('N+' === $row['Arztbegleitet']) {
            $entity->setIsWithPhysician(true);
        } else {
            $entity->setIsWithPhysician(false);
        }
        if ('BG+' === $row['Arbeits-/Wege-/Schulunfall']) {
            $entity->setIsWorkAccident(true);
        } else {
            $entity->setIsWorkAccident(false);
        }

        return $entity;
    }
}
