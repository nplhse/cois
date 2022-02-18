<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Traits\EventDispatcherTrait;
use App\Domain\Command\Import\DeleteImportCommand;
use App\Domain\Event\Import\ImportDeletedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Repository\AllocationRepository;

class DeleteImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    private ImportRepositoryInterface $importRepository;

    private AllocationRepository $allocationRepository;

    public function __construct(ImportRepositoryInterface $importRepository, AllocationRepository $allocationRepository)
    {
        $this->importRepository = $importRepository;
        $this->allocationRepository = $allocationRepository;
    }

    public function __invoke(DeleteImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find import: '.$command->getId());
        }

        $this->allocationRepository->deleteByImport($import);
        $this->importRepository->delete($import);

        $this->dispatchEvent(new ImportDeletedEvent($import));
    }
}
