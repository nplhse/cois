<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Event\Import\ImportCreatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportDataHandler implements HandlerInterface
{
    private ImportRepositoryInterface $importRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(ImportRepositoryInterface $importRepository, EventDispatcherInterface $dispatcher)
    {
        $this->importRepository = $importRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(ImportDataCommand $command): void
    {
        $import = new Import();
        $import->setName($command->getName());
        $import->setStatus(Import::STATUS_PENDING);
        $import->setType($command->getType());

        $import->setUser($command->getUser());
        $import->setHospital($command->getHospital());

        $import->setFilePath($command->getFilePath());
        $import->setFileMimeType($command->getFileMimeType());
        $import->setFileExtension($command->getFileExtension());
        $import->setFileSize($command->getFileSize());

        // LEGACY stuff
        $import->setContents($command->getType());
        $import->setPath($command->getFilePath());
        $import->setMimeType($command->getFileMimeType());
        $import->setSize($command->getFileSize());
        $import->setCaption($command->getName());
        $import->setExtension($command->getFileExtension());
        $import->setIsFixture(false);
        $import->setLastError(null);
        $import->setTimesRun(0);
        $import->setRowCount(0);
        $import->setRuntime(0);

        $this->importRepository->add($import);

        $event = new ImportCreatedEvent($import);

        $this->dispatcher->dispatch($event, ImportCreatedEvent::NAME);
    }
}
