<?php

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserEditedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.edited';

    public function __construct(
        private UserInterface $user
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
