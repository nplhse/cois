<?php

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Domain\Command\Import\EditImportCommand;
use App\Domain\Event\Import\ImportCreatedEvent;
use App\Domain\Repository\ImportRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EditImportHandler implements HandlerInterface
{
    private ImportRepositoryInterface $importRepository;

    private EventDispatcherInterface $dispatcher;

    public function __construct(ImportRepositoryInterface $importRepository, EventDispatcherInterface $dispatcher)
    {
        $this->importRepository = $importRepository;

        $this->dispatcher = $dispatcher;
    }

    public function __invoke(EditImportCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find Import with id %d', $command->getId());
        }

        $import->setName($command->getName());

        $this->importRepository->save();

        $event = new ImportCreatedEvent($import);

        $this->dispatcher->dispatch($event, ImportCreatedEvent::NAME);
    }
}
