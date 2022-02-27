<?php

namespace App\DataTransferObjects;

use App\Service\Filters\DateFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;

class FilterDto
{
    private bool $active = false;

    private array $filterItems = [];

    public function addFilter(string $key, mixed $value, array $altValues, string $type = 'string'): void
    {
        $this->filterItems[$key] = new FilterItemDto($key, $value, $altValues, $type);

        switch ($key) {
            case PageFilter::Param:
            case OrderFilter::Param:
                break;
            case DateFilter::Param:
                foreach ($value as $itemKey => $itemValue) {
                    if (!is_null($itemValue)) {
                        $this->active = true;
                    }
                }
                break;
            default:
                if (!is_null($value)) {
                    $this->active = true;
                }
                break;
        }
    }

    public function getFilter(string $key): ?FilterItemDto
    {
        return $this->filterItems[$key] ?? null;
    }

    public function issetFilter(string $key): bool
    {
        return isset($this->filterItems[$key]);
    }

    public function getFilters(): array
    {
        $filters = $this->filterItems;

        unset($filters[PageFilter::Param], $filters[OrderFilter::Param]);

        return $filters;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
