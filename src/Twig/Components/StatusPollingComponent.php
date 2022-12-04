<?php

namespace App\Twig\Components;

use App\Repository\ImportRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('status_polling')]
class StatusPollingComponent
{
    use DefaultActionTrait;

    public int $importId;

    public function __construct(
        private ImportRepository $importRepository
    ) {
    }

    public function getStatus(): string
    {
        $import = $this->importRepository->findOneBy(['id' => $this->importId]);

        return $import->getStatus();
    }

    public function getId(): string
    {
        $import = $this->importRepository->findOneBy(['id' => $this->importId]);

        return $import->getStatus();
    }
}
