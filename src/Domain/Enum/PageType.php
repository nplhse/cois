<?php

declare(strict_types=1);

namespace Domain\Enum;

enum PageType: string
{
    case GENERIC = 'Generic';
    case IMPRINT = 'Imprint';
    case TERMS = 'Terms';
    case PRIVACY = 'Privacy';
    case ABOUT = 'About';

    public function getType(): string
    {
        return match ($this) {
            PageType::GENERIC => PageType::GENERIC->value,
            PageType::IMPRINT => PageType::IMPRINT->value,
            PageType::TERMS => PageType::TERMS->value,
            PageType::PRIVACY => PageType::PRIVACY->value,
            PageType::ABOUT => PageType::ABOUT->value,
        };
    }
}
