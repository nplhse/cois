<?php

declare(strict_types=1);

namespace App\Domain\Event\Import;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class ImportFailedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'import.failed';

    public function __construct(
        private ImportInterface $import,
        private ?\Exception $exception
    ) {
    }

    public function getImport(): ImportInterface
    {
        return $this->import;
    }

    public function getException(): ?\Exception
    {
        return $this->exception;
    }
}
