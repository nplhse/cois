<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Import\UpdateImportCommand;
use App\Domain\Event\Import\ImportUpdatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Entity\Import;

class UpdateImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private ImportRepositoryInterface $importRepository;

    public function __construct(ImportRepositoryInterface $importRepository)
    {
        $this->importRepository = $importRepository;
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

        $this->dispatchEvent(new ImportUpdatedEvent($import));
    }
}
