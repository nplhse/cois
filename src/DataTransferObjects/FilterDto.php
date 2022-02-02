<?php

namespace App\DataTransferObjects;

class FilterDto
{
    private array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function set(string $key, mixed $value): void
    {
        $this->filters[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->filters[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->filters;
    }
}
