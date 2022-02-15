<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Event\Import\ImportSuccessEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Service\ImportService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

        if (null === $import->getFilePath()) {
            $path = $this->projectDir.self::Import_DIR.$import->getPath();
            $result = $this->importService->import($path, $import->getMimeType());
        } elseif ('' === $import->getFilePath()) {
            $path = $this->projectDir.self::Import_DIR.$import->getPath();
            $result = $this->importService->import($path, $import->getMimeType());
        } else {
            $path = $this->projectDir.self::Import_DIR.$import->getFilePath();
            $result = $this->importService->import($path, $import->getFileMimeType());
        }

        $this->importService->process($result, $import);

        $this->dispatcher->dispatch(new ImportSuccessEvent($import), ImportSuccessEvent::NAME);
    }
}
