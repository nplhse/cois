<?php

namespace App\Service\Import\Writer;

use App\Application\Contract\AllocationImportWriterInterface;
use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;

class AllocationImportWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    private array $headers;

    /**
     * @var iterable|array<AllocationImportWriterInterface>
     */
    private iterable $allocationImportWriter;

    public function __construct(iterable $allocationImportWriter)
    {
        $this->allocationImportWriter = $allocationImportWriter;
    }

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 100;
    }

    public function addHeaders(array $headers): void
    {
        $this->headers = array_flip($headers);
    }

    public function processData(array $row, ImportInterface $import): ?object
    {
        $entity = new Allocation();

        $entity->setImport($import);
        $entity->setHospital($import->getHospital());

        foreach ($this->allocationImportWriter as $unit) {
            $unit->process($entity, $this->headers);
        }

        return $entity;
    }
}
