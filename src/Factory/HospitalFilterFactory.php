<?php

declare(strict_types=1);

namespace App\Factory;

use App\Form\Filters\HospitalFilterSetType;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\HospitalOwnerFilter;
use App\Service\Filters\LocationFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\SizeFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;
use App\Service\Filters\TierFilter;

class HospitalFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            LocationFilter::Param,
            SizeFilter::Param,
            StateFilter::Param,
            TierFilter::Param,
            DispatchAreaFilter::Param,
            SupplyAreaFilter::Param,
            OwnHospitalFilter::Param,
            HospitalOwnerFilter::Param,
            PageFilter::Param,
            SearchFilter::Param,
            OrderFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return HospitalFilterSetType::class;
    }
}
