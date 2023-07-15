<?php

declare(strict_types=1);

namespace Domain\Event\User;

use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedPasswordEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.reset_credentials';

    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
