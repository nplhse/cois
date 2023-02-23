<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum HospitalSize: string
{
    case SMALL = 'Small';
    case MEDIUM = 'Medium';
    case LARGE = 'Large';

    public function getType(): string
    {
        return match ($this) {
            HospitalSize::SMALL => HospitalSize::SMALL->value,
            HospitalSize::MEDIUM => HospitalSize::MEDIUM->value,
            HospitalSize::LARGE => HospitalSize::LARGE->value,
        };
    }

    public static function getValues(): array
    {
        return [
            self::SMALL->value,
            self::MEDIUM->value,
            self::LARGE->value,
        ];
    }
}
