<?php

declare(strict_types=1);

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Traits\EventDispatcherTrait;
use Domain\Command\Import\EditImportCommand;
use Domain\Event\Import\ImportCreatedEvent;
use Domain\Repository\ImportRepositoryInterface;

class EditImportHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public function __construct(
        private ImportRepositoryInterface $importRepository
    ) {
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
