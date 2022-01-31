<?php

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedUsernameEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.changed_username';

    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
