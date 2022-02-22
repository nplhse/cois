<?php

namespace App\DataTransferObjects;

class FilterDto
{
    private array $filters;

    private array $altValues;

    private bool $active = false;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function set(string $key, mixed $value, mixed $altValue = null): void
    {
        $this->filters[$key] = $value;

        if ($altValue) {
            $this->altValues[$key] = $altValue;
        }
    }

    public function get(string $key): mixed
    {
        return $this->filters[$key] ?? null;
    }

    public function getAltValue(string $key): mixed
    {
        return $this->altValues[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->filters;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
