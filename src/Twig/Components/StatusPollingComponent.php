<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\ImportRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('status_polling')]
class StatusPollingComponent
{
    use DefaultActionTrait;

    #[LiveProp()]
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

    public function getId(): int
    {
        $import = $this->importRepository->findOneBy(['id' => $this->importId]);

        return $import->getId();
    }
}
