<?php

declare(strict_types=1);

namespace Domain\Enum;

use Domain\Adapter\EnumInterface;

abstract class AbstractBasicEnum implements EnumInterface
{
    private static array $cache = [];

    private static function getConstants(): array
    {
        $calledClass = static::class;

        if (!array_key_exists($calledClass, self::$cache)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$cache[$calledClass] = $reflect->getConstants();
        }

        return self::$cache[$calledClass];
    }

    public static function isValidName(string $name, bool $strict = false): bool
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return in_array(strtolower($name), $keys, $strict);
    }

    public static function isValidValue(string $value, bool $strict = true): bool
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values, $strict);
    }

    public static function getChoices(): array
    {
        return self::getConstants();
    }
}
