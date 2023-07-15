<?php

declare(strict_types=1);

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Traits\EventDispatcherTrait;
use App\Entity\Import;
use Domain\Command\Import\CreateImportCommand;
use Domain\Event\Import\ImportCreatedEvent;
use Domain\Repository\ImportRepositoryInterface;

class CreateImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private ImportRepositoryInterface $importRepository
    ) {
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

        $this->importRepository->add($import);

        $this->dispatchEvent(new ImportCreatedEvent($import));
    }
}
