<?php

declare(strict_types=1);

namespace App\Domain\Event\Task;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class TaskFailedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'task.failed';

    private \Exception $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getException(): \Exception
    {
        return $this->exception;
    }
}
