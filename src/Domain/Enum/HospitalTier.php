<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum HospitalTier: string
{
    case BASIC = 'Basic';
    case EXTENDED = 'Extended';
    case FULL = 'Full';

    public function getType(): string
    {
        return match ($this) {
            HospitalTier::BASIC => HospitalTier::BASIC->value,
            HospitalTier::EXTENDED => HospitalTier::EXTENDED->value,
            HospitalTier::FULL => HospitalTier::FULL->value,
        };
    }

    public static function getValues(): array
    {
        return [
            self::BASIC->value,
            self::EXTENDED->value,
            self::FULL->value,
        ];
    }
}
