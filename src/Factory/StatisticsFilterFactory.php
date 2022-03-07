<?php

namespace App\Factory;

use App\Form\Filters\StatisticsFilterSetType;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\OwnHospitalFilter;

class StatisticsFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            HospitalFilter::Param,
            OwnHospitalFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return StatisticsFilterSetType::class;
    }
}
