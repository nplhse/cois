<?php

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedUsernameEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.changed_username';

    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
