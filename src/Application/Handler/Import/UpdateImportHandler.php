<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Domain\Command\Import\UpdateImportCommand;
use App\Domain\Event\Import\ImportUpdatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateImportHandler implements HandlerInterface
{
    private ImportRepositoryInterface $importRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(ImportRepositoryInterface $importRepository, EventDispatcherInterface $dispatcher)
    {
        $this->importRepository = $importRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(UpdateImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        $import->setName($command->getName());
        $import->setType($command->getType());

        $import->setUser($command->getUser());
        $import->setHospital($command->getHospital());

        if ($command->getUpdateFile()) {
            $import->setStatus(Import::STATUS_PENDING);

            $import->setFile(null);
            $import->setFilePath($command->getFilePath());
            $import->setFileMimeType($command->getFileMimeType());
            $import->setFileExtension($command->getFileExtension());
            $import->setFileSize($command->getFileSize());

            $import->setLastError(null);
            $import->setTimesRun(0);
            $import->setRowCount(0);
            $import->setRuntime(0);
        }

        // LEGACY stuff
        $import->setContents($command->getType());
        $import->setPath($command->getFilePath());
        $import->setMimeType($command->getFileMimeType());
        $import->setSize($command->getFileSize());
        $import->setExtension($command->getFileExtension());
        $import->setCaption($command->getName());
        $import->setIsFixture(false);

        $this->importRepository->save();

        $event = new ImportUpdatedEvent($import);

        $this->dispatcher->dispatch($event, ImportUpdatedEvent::NAME);
    }
}
