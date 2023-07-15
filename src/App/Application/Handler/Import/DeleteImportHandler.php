<?php

declare(strict_types=1);

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Traits\EventDispatcherTrait;
use App\Repository\AllocationRepository;
use App\Repository\SkippedRowRepository;
use Domain\Command\Import\DeleteImportCommand;
use Domain\Event\Import\ImportDeletedEvent;
use Domain\Repository\ImportRepositoryInterface;

class DeleteImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private ImportRepositoryInterface $importRepository,
        private AllocationRepository $allocationRepository,
        private SkippedRowRepository $skippedRowRepository
    ) {
    }

    public function __invoke(DeleteImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find import: '.$command->getId());
        }

        $this->skippedRowRepository->deleteByImport($import);
        $this->allocationRepository->deleteByImport($import);
        $this->importRepository->delete($import);

        $this->dispatchEvent(new ImportDeletedEvent($import));
    }
}
