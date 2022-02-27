<?php

namespace App\Factory;

use App\Form\Filters\SearchType;
use App\Service\Filters\SearchFilter;

class SearchFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            SearchFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return SearchType::class;
    }
}
