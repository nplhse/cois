<?php

declare(strict_types=1);

namespace Domain\Event\Task;

use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class TaskFailedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'task.failed';

    public function __construct(
        private \Exception $exception
    ) {
    }

    public function getException(): \Exception
    {
        return $this->exception;
    }
}
