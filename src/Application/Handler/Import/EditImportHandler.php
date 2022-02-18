<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Import\EditImportCommand;
use App\Domain\Event\Import\ImportCreatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;

class EditImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private ImportRepositoryInterface $importRepository;

    public function __construct(ImportRepositoryInterface $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function __invoke(EditImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find Import with id %d', $command->getId());
        }

        $import->setName($command->getName());

        $this->importRepository->save();

        $this->dispatchEvent(new ImportCreatedEvent($import));
    }
}
