<?php

declare(strict_types=1);

namespace App\Service\Import\Writer\Allocation;

use App\Entity\Allocation;
use Domain\Contracts\ImportInterface;

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

        return $entity;
    }
}
