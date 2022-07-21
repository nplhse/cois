<?php

namespace App\Service\Import\Writer;

use App\Application\Contract\AllocationImportWriterInterface;
use App\Application\Exception\ImportValidationException;
use App\Domain\Contracts\ImportInterface;
use App\Entity\Allocation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AllocationImportWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    /**
     * @var iterable|array<AllocationImportWriterInterface>
     */
    private iterable $allocationImportWriter;

    private ValidatorInterface $validator;

    public function __construct(iterable $allocationImportWriter, ValidatorInterface $validator)
    {
        $this->allocationImportWriter = $allocationImportWriter;
        $this->validator = $validator;
    }

    public static function getDataType(): string
    {
        return self::Data_Type;
    }

    public static function getPriority(): int
    {
        return 100;
    }

    public function processData(array $row, ImportInterface $import): ?object
    {
        $entity = $this->getEntity();

        $entity->setImport($import);
        $entity->setHospital($import->getHospital());

        foreach ($this->allocationImportWriter as $unit) {
            $unit->process($entity, $row, $import);
        }

        $this->validateEntity($entity);

        return $entity;
    }

    private function getEntity(): object
    {
        return new Allocation();
    }

    private function validateEntity(object $entity): void
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $errorMessage = '';
            foreach ($errors as $error) {
                $errorMessage = $error->getMessage()."\n";
            }

            throw new ImportValidationException($errorMessage);
        }
    }
}
