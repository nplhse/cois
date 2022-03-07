<?php

namespace App\Factory;

use App\Form\Filters\StatisticsFilterSetType;
use App\Service\Filters\DateFilter;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;

class StatisticsFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            HospitalFilter::Param,
            OwnHospitalFilter::Param,
            DateFilter::Param,
            StateFilter::Param,
            DispatchAreaFilter::Param,
            SupplyAreaFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return StatisticsFilterSetType::class;
    }
}
