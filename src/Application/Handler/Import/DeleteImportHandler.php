<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Domain\Command\Import\DeleteImportCommand;
use App\Domain\Event\Import\ImportDeletedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use App\Repository\AllocationRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteImportHandler implements HandlerInterface
{
    private ImportRepositoryInterface $importRepository;

    private AllocationRepository $allocationRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(ImportRepositoryInterface $importRepository, AllocationRepository $allocationRepository, EventDispatcherInterface $dispatcher)
    {
        $this->importRepository = $importRepository;
        $this->allocationRepository = $allocationRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeleteImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find import: '.$command->getId());
        }

        $this->allocationRepository->deleteByImport($import);
        $this->importRepository->delete($import);

        $event = new ImportDeletedEvent($import);

        $this->dispatcher->dispatch($event, ImportDeletedEvent::NAME);
    }
}
