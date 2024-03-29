<?php

declare(strict_types=1);

namespace App\Application\Handler\Import;

use App\Application\Contract\HandlerInterface;
use App\Application\Exception\ImportNotFoundException;
use App\Application\Traits\EventDispatcherTrait;
use App\Service\Import\ImportService;
use Domain\Command\Import\ImportDataCommand;
use Domain\Event\Import\ImportSuccessEvent;
use Domain\Repository\ImportRepositoryInterface;

class ImportDataHandler implements HandlerInterface
{
    use EventDispatcherTrait;

    public const Import_DIR = '/var/storage/import/';

    public function __construct(
        private ImportRepositoryInterface $importRepository,
        private ImportService $importService,
        private string $projectDir
    ) {
    }

    public function __invoke(ImportDataCommand $command): void
    {
        $import = $this->importRepository->findOneBy(['id' => $command->getImportId()]);

        if (!$import) {
            throw new ImportNotFoundException('Could not find Import with id %d', $command->getImportId());
        }

        $path = $this->projectDir.self::Import_DIR.$import->getFilePath();
        $result = $this->importService->import($path, $import->getFileMimeType());

        $this->importService->process($result, $import);

        $this->dispatchEvent(new ImportSuccessEvent($import));
    }
}
