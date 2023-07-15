<?php

declare(strict_types=1);

namespace Domain\Event\User;

use Domain\Contracts\DomainEventInterface;
use Domain\Event\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserPromotedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.promoted';

    public function __construct(
        private int $id,
        private bool $isVerified,
        private bool $isParticipant
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function getIsParticipant(): bool
    {
        return $this->isParticipant;
    }
}
