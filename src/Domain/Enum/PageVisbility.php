<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum PageVisbility: string
{
    case PUBLIC = 'Public';
    case PRIVATE = 'Private';

    public function getType(): string
    {
        return match ($this) {
            PageVisbility::PUBLIC => PageVisbility::PUBLIC->value,
            PageVisbility::PRIVATE => PageVisbility::PRIVATE->value,
        };
    }
}
