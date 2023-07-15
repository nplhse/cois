<?php

declare(strict_types=1);

namespace Domain\Enum;

enum HospitalLocation: string
{
    case URBAN = 'Urban';
    case RURAL = 'Rural';

    public function getType(): string
    {
        return match ($this) {
            HospitalLocation::URBAN => HospitalLocation::URBAN->value,
            HospitalLocation::RURAL => HospitalLocation::RURAL->value,
        };
    }

    public static function getValues(): array
    {
        return [
            self::URBAN->value,
            self::RURAL->value,
        ];
    }
}
