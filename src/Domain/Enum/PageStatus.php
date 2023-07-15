<?php

declare(strict_types=1);

namespace Domain\Enum;

enum PageStatus: string
{
    case DRAFT = 'Draft';
    case PUBLISHED = 'Published';

    public function getType(): string
    {
        return match ($this) {
            PageStatus::DRAFT => PageStatus::DRAFT->value,
            PageStatus::PUBLISHED => PageStatus::PUBLISHED->value,
        };
    }
}
