<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TypeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('is_type', [$this, 'is_type']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_type', [$this, 'is_type']),
        ];
    }

    public function is_type(mixed $value, string $test): bool
    {
        return match ($test) {
            'array' => \is_array($value),
            'bool' => \is_bool($value),
            'object' => \is_object($value),
            'float' => \is_float($value),
            'integer' => \is_int($value),
            'numeric' => is_numeric($value),
            'scalar' => is_scalar($value),
            'string' => \is_string($value),
            'iterable' => \is_iterable($value),
            default => throw new \InvalidArgumentException(sprintf('Invalid "%s" type test.', $test)),
        };
    }
}
