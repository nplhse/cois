<?php

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisteredEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.registered';

    private UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
