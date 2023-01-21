<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum CommentStatus: string
{
    case SUBMITTED = 'Submitted';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';

    /**
     * @return array<string,string>
     */
    public static function getAsArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, CommentStatus $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}
