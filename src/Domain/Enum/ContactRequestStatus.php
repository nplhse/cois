<?php

declare(strict_types=1);

namespace Domain\Enum;

enum ContactRequestStatus: string
{
    case OPEN = 'Open';
    case CLOSED = 'Closed';
    case REJECTED = 'Rejected';

    public function getType(): string
    {
        return match ($this) {
            ContactRequestStatus::OPEN => ContactRequestStatus::OPEN->value,
            ContactRequestStatus::CLOSED => ContactRequestStatus::CLOSED->value,
            ContactRequestStatus::REJECTED => ContactRequestStatus::REJECTED->value,
        };
    }
}
