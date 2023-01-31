<?php

declare(strict_types=1);

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserChangedProfileEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.changed_profile';

    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
