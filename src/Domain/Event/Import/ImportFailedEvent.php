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

    private ImportInterface $import;

    private \Exception $exception;

    public function __construct(ImportInterface $import, ?\Exception $exception)
    {
        $this->import = $import;
        $this->exception = $exception;
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
