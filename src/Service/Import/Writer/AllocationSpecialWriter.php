<?php

namespace App\Service\Import\Writer;

use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationSpecialWriter implements \App\Application\Contract\ImportWriterInterface
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
        /* @var Allocation $entity */
        $entity->setUrgency((int) substr($row['PZC'], 5, 1));
        $entity->setIndicationCode((int) substr($row['PZC'], 0, 3));

        if (isset($row['Neben-PZC'])) {
            $entity->setSecondaryIndicationCode((int) substr($row['Neben-PZC'], 0, 3));
        }

        return $entity;
    }
}
