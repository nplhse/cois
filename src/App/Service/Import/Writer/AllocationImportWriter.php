<?php

declare(strict_types=1);

namespace App\Service\Import\Writer;

use App\Application\Exception\ImportValidationException;
use App\Entity\Allocation;
use Domain\Contracts\ImportInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AllocationImportWriter implements \App\Application\Contract\ImportWriterInterface
{
    public const Data_Type = 'allocation';

    /**
     * @param \App\Application\Contract\AllocationImportWriterInterface[] $allocationImportWriter
     */
    public function __construct(
        private iterable $allocationImportWriter,
        private ValidatorInterface $validator
    ) {
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
