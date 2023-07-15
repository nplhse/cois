<?php

declare(strict_types=1);

namespace Domain\Event\Import;

use Domain\Contracts\DomainEventInterface;
use Domain\Contracts\ImportInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class ImportUpdatedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'import.updated';

    public function __construct(
        private ImportInterface $import
    ) {
    }

    public function getImport(): ImportInterface
    {
        return $this->import;
    }
}
