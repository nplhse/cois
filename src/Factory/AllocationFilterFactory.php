<?php

namespace App\Factory;

use App\Form\Filters\AllocationFilterSetType;
use App\Service\Filters\AssignmentFilter;
use App\Service\Filters\DateFilter;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\HospitalOwnerFilter;
use App\Service\Filters\ImportFilter;
use App\Service\Filters\IndicationFilter;
use App\Service\Filters\InfectionFilter;
use App\Service\Filters\IsCPRFilter;
use App\Service\Filters\IsPregnantFilter;
use App\Service\Filters\IsShockFilter;
use App\Service\Filters\IsVentilatedFilter;
use App\Service\Filters\IsWithPhysicianFilter;
use App\Service\Filters\IsWorkAccidentFilter;
use App\Service\Filters\ModeOfTransportFilter;
use App\Service\Filters\OccasionFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\RequiresCathlabFilter;
use App\Service\Filters\RequiresResusFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\SecondaryDeploymentFilter;
use App\Service\Filters\SpecialityDetailFilter;
use App\Service\Filters\SpecialityFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;
use App\Service\Filters\UrgencyFilter;

class AllocationFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            HospitalFilter::Param,
            ImportFilter::Param,
            DateFilter::Param,
            IndicationFilter::Param,
            AssignmentFilter::Param,
            InfectionFilter::Param,
            ModeOfTransportFilter::Param,
            OccasionFilter::Param,
            RequiresResusFilter::Param,
            RequiresCathlabFilter::Param,
            SpecialityFilter::Param,
            SpecialityDetailFilter::Param,
            UrgencyFilter::Param,
            IsCPRFilter::Param,
            IsPregnantFilter::Param,
            IsShockFilter::Param,
            IsVentilatedFilter::Param,
            IsWithPhysicianFilter::Param,
            IsWorkAccidentFilter::Param,
            StateFilter::Param,
            DispatchAreaFilter::Param,
            SupplyAreaFilter::Param,
            OwnHospitalFilter::Param,
            HospitalOwnerFilter::Param,
            PageFilter::Param,
            SearchFilter::Param,
            OrderFilter::Param,
            SecondaryDeploymentFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return AllocationFilterSetType::class;
    }
}
