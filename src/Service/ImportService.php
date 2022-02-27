<?php

namespace App\Service;

use App\Application\Contract\ImportReaderInterface;
use App\Application\Contract\ImportWriterInterface;
use App\Application\Exception\ImportReaderNotFoundException;
use App\Application\Exception\ImportWriteException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Allocation;
use App\Entity\Import;
use App\Entity\SkippedRow;
use App\Service\Import\Reader\CsvImportReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportService
{
    use EventDispatcherTrait;

    /**
     * @var iterable|array<ImportReaderInterface>
     */
    private iterable $importReader;

    /**
     * @var iterable|array<ImportWriterInterface>
     */
    private iterable $importWriter;

    private EntityManagerInterface $em;

    private Stopwatch $stopwatch;

    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, Stopwatch $stopwatch, ValidatorInterface $validator, iterable $importReader, iterable $importWriter)
    {
        // Important: Disable SQL logging!
        $this->em = $entityManager;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->stopwatch = $stopwatch;
        $this->validator = $validator;

        $this->importReader = $importReader instanceof \Traversable ? iterator_to_array($importReader) : $importReader;
        $this->importWriter = $importWriter instanceof \Traversable ? iterator_to_array($importWriter) : $importWriter;
    }

    public function import(string $path, string $fileType): iterable
    {
        if (array_key_exists('$fileType', $this->importReader)) {
            return $this->importReader[$fileType]->importData($path);
        }

        // If there is no fileType, just assume it can be parsed as CSV
        if (empty($fileType)) {
            return $this->importReader[CsvImportReader::File_Type]->importData($path);
        }

        foreach ($this->importReader as $reader) {
            if ($reader->getAlias() && in_array($fileType, $reader->getAlias(), true)) {
                return $this->importReader[$reader::getFileType()]->importData($path);
            }
        }

        throw new ImportReaderNotFoundException(sprintf('ImportReader %s not found', $fileType));
    }

    public function process(iterable $result, Import $import): void
    {
        $activeWriters = [];
        $iteration = 0;

        foreach ($this->importWriter as $writer) {
            if ($writer::getDataType() === $import->getType()) {
                $activeWriters[] = $writer;
            }
        }

        $this->stopwatch->start('import-data');

        try {
            foreach ($result as $row) {
                ++$iteration;
                $entity = null;

                try {
                    foreach ($activeWriters as $writer) {
                        $entity = $writer->processData($entity, $row, $import);
                    }

                    $errors = $this->validator->validate($entity);

                    if (count($errors) > 0) {
                        $errorMessage = '';
                        foreach ($errors as $error) {
                            $errorMessage = $error->getMessage().'\n';
                        }

                        $skippedRow = new SkippedRow();
                        $skippedRow->setImport($import);
                        $skippedRow->setErrors($errorMessage);
                        $skippedRow->setData($row);

                        $this->em->persist($skippedRow);

                        $import->addSkippedRow();
                        continue;
                    }
                } catch (\InvalidArgumentException $e) {
                    $skippedRow = new SkippedRow();
                    $skippedRow->setImport($import);
                    $skippedRow->setErrors($e->getMessage());
                    $skippedRow->setData($row);

                    $this->em->persist($skippedRow);

                    $import->addSkippedRow();
                    continue;
                }

                $this->em->persist($entity);

                // Persist flush only 500 entities at once via $entityStore
                $entityStore[] = $entity;

                if (0 === $iteration % 500) {
                    $this->em->flush();

                    foreach ($entityStore as $tempEntity) {
                        $this->em->detach($tempEntity);
                    }

                    $entityStore = null;

                    gc_enable();
                    gc_collect_cycles();
                }
            }

            $import->setStatus(Import::STATUS_SUCCESS);
        } catch (ImportWriteException|\Exception $e) {
            $import->setStatus(Import::STATUS_FAILURE);
            $this->dispatchEvent(new ImportFailedEvent($import, $e));
        }

        if (Import::STATUS_FAILURE !== $import->getStatus()) {
            if ($this->getPercentage($iteration, $import->getSkippedRows()) > 0.25 && $this->getPercentage($iteration, $import->getSkippedRows()) < 5) {
                $import->setStatus(Import::STATUS_INCOMPLETE);
            } elseif ($this->getPercentage($iteration, $import->getSkippedRows()) > 5) {
                $this->dispatchEvent(new ImportFailedEvent($import, new ImportWriteException('Too many skipped rows in import.')));

                $import->setStatus(Import::STATUS_FAILURE);

                $allocationRepository = $this->em->getRepository(Allocation::class);
                $allocationRepository->deleteByImport($import);
            }
        }

        $import->bumpRunCount();
        $import->setRowCount($iteration);
        $import->setRuntime((int) $this->stopwatch->stop('import-data')->getDuration());

        $this->em->flush();
    }

    private function getPercentage(int $total, int $number): float
    {
        if ($total > 0) {
            return round(($number * 100) / $total, 2);
        }

        return 0;
    }
}
