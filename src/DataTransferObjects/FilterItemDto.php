<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

class FilterItemDto implements \Stringable
{
    public function __construct(
        private string $key,
        private mixed $value,
        private array $altValues,
        private string $type
    ) {
    }

    public function getKey(): string
    {
        return ucfirst($this->key);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getAltValue(): mixed
    {
        if (is_array($this->value)) {
            return null;
        }

        return $this->altValues[$this->value] ?? null;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
