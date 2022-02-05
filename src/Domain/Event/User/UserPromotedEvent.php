<?php

namespace App\Domain\Event\User;

use App\Domain\Contracts\DomainEventInterface;
use App\Domain\Event\Traits\NamedEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserPromotedEvent extends Event implements DomainEventInterface
{
    use NamedEventTrait;

    public const NAME = 'user.promoted';

    private int $id;

    private bool $isVerified;

    private bool $isParticipant;

    public function __construct(int $id, bool $isVerified, bool $isParticipant)
    {
        $this->id = $id;
        $this->isVerified = $isVerified;
        $this->isParticipant = $isParticipant;
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
