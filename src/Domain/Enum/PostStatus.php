<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum PostStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';

    /**
     * @return array<string,string>
     */
    public static function getAsArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, PostStatus $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}
