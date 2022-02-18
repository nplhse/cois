<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Import\CreateImportCommand;
use App\Domain\Event\Import\ImportCreatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;

class CreateImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private ImportRepositoryInterface $importRepository;

    public function __construct(ImportRepositoryInterface $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function __invoke(CreateImportCommand $command): void
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

        $this->dispatchEvent(new ImportCreatedEvent($import));
    }
}
