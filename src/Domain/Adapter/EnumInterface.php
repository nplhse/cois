<?php

namespace App\Domain\Adapter;

interface EnumInterface
{
    public static function isValidName(string $name, bool $strict = false): bool;

    public static function isValidValue(string $value, bool $strict = true): bool;
}
