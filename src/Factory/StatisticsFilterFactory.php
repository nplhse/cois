<?php

namespace App\Factory;

use App\Form\Filters\StatisticsFilterSetType;
use App\Service\Filters\HospitalFilter;

class StatisticsFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            HospitalFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return StatisticsFilterSetType::class;
    }
}
