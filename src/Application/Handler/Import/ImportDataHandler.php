<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportMimeTypeNotSupportedException;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Exception\ImportTypeNotSupported;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Event\Import\ImportSuccessEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;
use App\Service\ImportService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImportDataHandler implements HandlerInterface
{
    public const Import_DIR = '/var/storage/import/';

    private ImportRepositoryInterface $importRepository;

    private ImportService $importService;

    private EventDispatcherInterface $dispatcher;

    private string $projectDir;

    public function __construct(ImportRepositoryInterface $importRepository, ImportService $importService, EventDispatcherInterface $dispatcher, string $projectDir)
    {
        $this->importRepository = $importRepository;
        $this->importService = $importService;
        $this->dispatcher = $dispatcher;

        $this->projectDir = $projectDir;
    }

    public function __invoke(ImportDataCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getImportId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find Import with id %d', $command->getImportId());
        }

        $path = $this->projectDir.self::Import_DIR.$import->getFilePath();

        $result = $this->importService->import($path, $import->getFileMimeType());
        $this->importService->process($result, $import);

        //$result = $this->loadData($import);
        // $this->processData($result, $import);

        $this->dispatcher->dispatch(new ImportSuccessEvent($import), ImportSuccessEvent::NAME);
    }

    private function loadData(ImportInterface $import): iterable
    {
        $filesystem = new Filesystem();

        $path = $this->projectDir.self::Import_DIR.$import->getFilePath();
        $result = match ($import->getFileMimeType()) {
            \App\Domain\Entity\Import::MIME_CSV, \App\Domain\Entity\Import::MIME_PLAIN => $this->importService->import($path, $import->getFileMimeType()),
            default => throw new ImportMimeTypeNotSupportedException(sprintf('MimeType %s of Import %d is not supported.', $import->getFileMimeType(), $import->getId())),
        };

        return $result;
    }

    private function processData(iterable $result, ImportInterface $import): void
    {
        switch ($import->getType()) {
            case Import::TYPE_ALLOCATION:
                 $this->importService->processToAllocation($result, $import);
                break;
            default:
                throw new ImportTypeNotSupported(sprintf('Import type %s of Import %d is not supported.', $import->getType(), $import->getId()));
        }
    }
}
