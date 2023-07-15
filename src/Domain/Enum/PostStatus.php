<?php

declare(strict_types=1);

namespace Domain\Enum;

enum PostStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';

    public function getType(): string
    {
        return match ($this) {
            PostStatus::Draft => PostStatus::Draft->value,
            PostStatus::Published => PostStatus::Published->value,
        };
    }

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
