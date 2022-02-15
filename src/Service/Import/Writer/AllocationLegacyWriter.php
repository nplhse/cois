<?php

namespace App\Service\Import\Writer;

use App\Domain\Contracts\ImportInterface;

class AllocationLegacyWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 0;
    }

    public function processData(?object $entity, array $row, ImportInterface $import): object
    {
        $entity->setPZC((int) $row['PZC']);
        $entity->setPZCText($row['PZC-Text']);
        $entity->setSecondaryPZC(null);
        $entity->setSecondaryPZCText($row['Neben-PZC-Text']);
        $entity->setRMI((int) substr($row['PZC'], 0, 3));
        $entity->setSK((int) substr($row['PZC'], 5, 1));

        return $entity;
    }
}
