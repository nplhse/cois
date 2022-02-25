<?php

namespace App\Service;

use App\Application\Contract\ImportReaderInterface;
use App\Application\Contract\ImportWriterInterface;
use App\Application\Exception\ImportReaderNotFoundException;
use App\Application\Exception\ImportWriteException;
use App\Domain\Event\Import\ImportSkippedRowEvent;
use App\Entity\Import;
use App\Service\Import\Reader\CsvImportReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportService
{
    private array $result = [];

    private float $runtime;

    /**
     * @var iterable|array<ImportReaderInterface>
     */
    private iterable $importReader;

    /**
     * @var iterable|array<ImportWriterInterface>
     */
    private iterable $importWriter;

    private EntityManagerInterface $em;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, iterable $importReader, iterable $importWriter)
    {
        // Important: Disable SQL logging!
        $this->em = $entityManager;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->eventDispatcher = $eventDispatcher;

        $this->importReader = $importReader instanceof \Traversable ? iterator_to_array($importReader) : $importReader;
        $this->importWriter = $importWriter instanceof \Traversable ? iterator_to_array($importWriter) : $importWriter;
    }

    public function getResult(): array
    {
        return $this->result;
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

        $this->startTime();

        try {
            foreach ($result as $row) {
                ++$iteration;
                $entity = null;

                try {
                    foreach ($activeWriters as $writer) {
                        $entity = $writer->processData($entity, $row, $import);
                    }
                } catch (\InvalidArgumentException $e) {
                    $this->eventDispatcher->dispatch(new ImportSkippedRowEvent($import, $e), ImportSkippedRowEvent::NAME);
                    continue;
                }

                $this->em->persist($entity);

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
        } catch (ImportWriteException $e) {
            $import->setLastError($e->getMessage());
            $import->setStatus(Import::STATUS_FAILURE);
        }

        $import->bumpRunCount();
        $import->setRowCount($iteration);
        $import->setRuntime((int) $this->stopTime());

        $this->em->flush();
    }

    private function startTime(): void
    {
        $this->runtime = microtime(true);
    }

    private function stopTime(): float
    {
        return microtime(true) - $this->runtime;
    }
}
