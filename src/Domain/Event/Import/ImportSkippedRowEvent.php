<?php

declare(strict_types=1);

namespace Domain\Event\Import;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\ImportInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class ImportSkippedRowEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'import.skipped_row';

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
