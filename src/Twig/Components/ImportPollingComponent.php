<?php

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

    private ImportRepository $importRepository;

    public function __construct(ImportRepository $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function getImport(): ?Import
    {
        return $this->importRepository->findOneBy(['id' => $this->importId]);
    }
}
