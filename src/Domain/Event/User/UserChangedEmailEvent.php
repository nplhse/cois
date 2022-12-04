<?php

declare(strict_types=1);

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedEmailEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.changed_email';

    public function __construct(
        private UserInterface $user
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
