<?php

declare(strict_types=1);

namespace App\Domain\Event\Import;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\ImportInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class ImportCreatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'import.created';

    private ImportInterface $import;

    public function __construct(ImportInterface $import)
    {
        $this->import = $import;
    }

    public function getImport(): ImportInterface
    {
        return $this->import;
    }
}
