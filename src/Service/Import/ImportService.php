<?php

namespace App\Service\Import;

use App\Application\Contract\ImportReaderInterface;
use App\Application\Contract\ImportWriterInterface;
use App\Application\Exception\ImportReaderNotFoundException;
use App\Application\Exception\ImportWriteException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Allocation;
use App\Entity\Import;
use App\Entity\SkippedRow;
use App\Helper\StatisticsHelper;
use App\Repository\AllocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ImportService
{
    use EventDispatcherTrait;

    public const CUTOFF_MIN = 1;

    public const CUTOFF_MAX = 10;

    /**
     * @var iterable|array<ImportReaderInterface>
     */
    private iterable $importReader;

    /**
     * @var iterable|array<ImportWriterInterface>
     */
    private iterable $importWriter;

    public function __construct(
        private EntityManagerInterface $em,
        private Stopwatch $stopwatch,
        iterable $importReader,
        iterable $importWriter
    ) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->importReader = $importReader instanceof \Traversable ? iterator_to_array($importReader) : $importReader;
        $this->importWriter = $importWriter instanceof \Traversable ? iterator_to_array($importWriter) : $importWriter;
    }

    public function import(string $path, string $fileType): iterable
    {
        // Check if primary fileType exists
        if (array_key_exists('$fileType', $this->importReader)) {
            return $this->importReader[$fileType]->importData($path);
        }

        // Check if alias fileType exists
        foreach ($this->importReader as $reader) {
            if ($reader->getAlias() && in_array($fileType, $reader->getAlias(), true)) {
                return $this->importReader[$reader::getFileType()]->importData($path);
            }
        }

        throw new ImportReaderNotFoundException(sprintf('ImportReader %s not found', $fileType));
    }

    public function process(iterable $result, Import $import): void
    {
        $iteration = 0;
        $import->resetSkippedRows();
        $activeWriters = $this->getActiveImportWriters($this->importWriter, $import);

        try {
            $this->stopwatch->start('import-data');

            foreach ($result as $row) {
                ++$iteration;

                try {
                    foreach ($activeWriters as $writer) {
                        $entity = $writer->processData($row, $import);

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
                } catch (\Exception $e) {
                    $this->addSkippedRow($import, $e->getMessage(), $row);
                    continue;
                }
            }

            // Check if any rows have been imported
            if ($iteration > 0) {
                $import->setStatus(Import::STATUS_SUCCESS);
            } else {
                $import->setStatus(Import::STATUS_EMPTY);
            }
        } catch (ImportWriteException|\Exception $e) {
            $import->setStatus(Import::STATUS_FAILURE);
            $this->dispatchEvent(new ImportFailedEvent($import, $e));
        }

        $import->bumpRunCount();
        $import->setRowCount($iteration);
        $import->setRuntime((int) $this->stopwatch->stop('import-data')->getDuration());

        $this->assessSkippedRows($import);

        $this->em->flush();
    }

    private function getActiveImportWriters(array $importWriter, \App\Domain\Entity\Import $import): array
    {
        $activeWriters = [];

        foreach ($importWriter as $writer) {
            if ($writer::getDataType() === $import->getType()) {
                $activeWriters[] = $writer;
            }
        }

        return $activeWriters;
    }

    private function addSkippedRow(Import $import, string $message, array $row): void
    {
        $skippedRow = new SkippedRow();
        $skippedRow->setImport($import);
        $skippedRow->setErrors($message);
        $skippedRow->setData($row);

        $this->em->persist($skippedRow);

        $import->addSkippedRow();
    }

    public function assessSkippedRows(Import $import): void
    {
        $percentage = StatisticsHelper::getPercentage($import->getRowCount(), $import->getSkippedRows());

        if ($percentage > self::CUTOFF_MIN && $percentage < self::CUTOFF_MAX) {
            $import->setStatus(Import::STATUS_INCOMPLETE);
        } elseif ($percentage >= self::CUTOFF_MAX) {
            $this->dispatchEvent(new ImportFailedEvent($import, new ImportWriteException('Too many skipped rows in import.')));

            $import->setStatus(Import::STATUS_FAILURE);

            /** @var AllocationRepository $allocationRepository */
            $allocationRepository = $this->em->getRepository(Allocation::class);
            $allocationRepository->deleteByImport($import);
        }
    }
}
