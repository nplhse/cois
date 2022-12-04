<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Import;
use App\Repository\ImportRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('import_polling')]
class ImportPollingComponent
{
    use DefaultActionTrait;

    public int $importId;

    public function __construct(
        private ImportRepository $importRepository
    ) {
    }

    public function getImport(): ?Import
    {
        return $this->importRepository->findOneBy(['id' => $this->importId]);
    }
}
