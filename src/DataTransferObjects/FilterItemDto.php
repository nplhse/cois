<?php

namespace App\DataTransferObjects;

class FilterItemDto implements \Stringable
{
    private string $key;

    private mixed $value = null;

    private string $type;

    private array $altValues;

    public function __construct(string $key, mixed $value, array $altValues, string $type)
    {
        $this->key = $key;
        $this->value = $value;
        $this->altValues = $altValues;
        $this->type = $type;
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