<?php

declare(strict_types=1);

namespace App\Service\Import\Writer\Allocation;

use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationSpecialWriter implements \App\Application\Contract\AllocationImportWriterInterface
{
    public function process(?object $entity, array $row, ImportInterface $import): ?object
    {
        if (!$entity instanceof Allocation) {
            return $entity;
        }

        $entity->setUrgency((int) substr($row['PZC'], 5, 1));
        $entity->setIndicationCode((int) substr($row['PZC'], 0, 3));

        if (isset($row['Neben-PZC'])) {
            $entity->setSecondaryIndicationCode((int) substr($row['Neben-PZC'], 0, 3));
        }

        if (isset($row['Sekundäranlass'])) {
            $entity->setSecondaryDeployment($row['Sekundäranlass']);
        }

        if (empty($row['PZC'])) {
            $value = match ($row['Dringlichkeit']) {
                'Ambulante Versorgung' => 3,
                'Stationäre Versorgung' => 2,
                'Station?re Versorgung' => 2,
                'Notfallversorgung' => 1,
                default => null,
            };

            $entity->setUrgency($value);
        }

        return $entity;
    }
}
