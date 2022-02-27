<?php

namespace App\Factory;

use App\Form\Filters\ImportFilterSetType;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\ImportStatusFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\OwnImportFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\UserFilter;

class ImportFilterFactory extends AbstractFilterFactory
{
    public function getFilters(): array
    {
        return [
            OwnImportFilter::Param,
            ImportStatusFilter::Param,
            UserFilter::Param,
            HospitalFilter::Param,
            OwnHospitalFilter::Param,
            PageFilter::Param,
            SearchFilter::Param,
            OrderFilter::Param,
        ];
    }

    public function getClass(): string
    {
        return ImportFilterSetType::class;
    }
}
